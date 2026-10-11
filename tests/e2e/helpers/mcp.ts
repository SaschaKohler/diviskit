import { spawn, type ChildProcess } from 'node:child_process';
import { loadState } from '../env';

/**
 * Minimal MCP stdio client. The MCP stdio transport is newline-delimited
 * JSON-RPC (one message per line, UTF-8, no framing headers) — NOT the
 * LSP-style Content-Length framing. Enough for
 * initialize → tools/list → tools/call without the SDK.
 */

interface JsonRpcMessage {
  id?: number;
  method?: string;
  result?: any;
  error?: { code: number; message: string; data?: any };
}

export class McpStdioClient {
  private child: ChildProcess;
  private buf = '';
  private nextId = 1;
  private pending = new Map<number, { resolve: (v: any) => void; reject: (e: Error) => void }>();
  private stderr = '';

  constructor(
    command: string,
    args: string[],
    env: Record<string, string>,
    private readyTimeoutMs = 120_000,
  ) {
    this.child = spawn(command, args, {
      env: { ...process.env, ...env },
      stdio: ['pipe', 'pipe', 'pipe'],
    });
    this.child.stdout!.on('data', (chunk: Buffer) => this.feed(chunk));
    this.child.stderr!.on('data', (chunk: Buffer) => {
      this.stderr += chunk.toString();
    });
    this.child.on('exit', (code) => {
      for (const { reject } of this.pending.values()) {
        reject(new Error(`mcp server exited (code ${code}). stderr:\n${this.stderr}`));
      }
      this.pending.clear();
    });
  }

  static spawnForSite(): McpStdioClient {
    const s = loadState();
    const command = process.env.DK_E2E_MCP_COMMAND ?? 'npx';
    const args: string[] = process.env.DK_E2E_MCP_ARGS
      ? JSON.parse(process.env.DK_E2E_MCP_ARGS)
      : ['-y', '--package', process.env.DK_E2E_MCP_PACKAGE ?? '@diviskit/mcp-server', 'diviskit-mcp'];
    return new McpStdioClient(command, args, {
      WP_URL: s.wpUrl,
      WP_USER: s.wpUser,
      WP_APP_PASSWORD: s.wpAppPassword,
      NODE_TLS_REJECT_UNAUTHORIZED: '0',
    });
  }

  private feed(chunk: Buffer) {
    this.buf += chunk.toString('utf8');
    for (;;) {
      const nl = this.buf.indexOf('\n');
      if (nl === -1) return;
      const line = this.buf.slice(0, nl).trim();
      this.buf = this.buf.slice(nl + 1);
      if (!line) continue;
      let msg: JsonRpcMessage;
      try {
        msg = JSON.parse(line);
      } catch {
        continue; // non-JSON stdout noise, skip
      }
      if (msg.id !== undefined && this.pending.has(msg.id)) {
        const p = this.pending.get(msg.id)!;
        this.pending.delete(msg.id);
        if (msg.error) p.reject(new Error(`JSON-RPC ${msg.error.code}: ${msg.error.message}`));
        else p.resolve(msg.result);
      }
    }
  }

  private send(msg: object) {
    this.child.stdin!.write(`${JSON.stringify(msg)}\n`);
  }

  request<T = any>(method: string, params?: object): Promise<T> {
    const id = this.nextId++;
    this.send({ jsonrpc: '2.0', id, method, ...(params ? { params } : {}) });
    return new Promise<T>((resolve, reject) => {
      const timer = setTimeout(
        () => reject(new Error(`timeout waiting for ${method}. stderr:\n${this.stderr}`)),
        this.readyTimeoutMs,
      );
      this.pending.set(id, {
        resolve: (v) => {
          clearTimeout(timer);
          resolve(v);
        },
        reject: (e) => {
          clearTimeout(timer);
          reject(e);
        },
      });
    });
  }

  notify(method: string, params?: object) {
    this.send({ jsonrpc: '2.0', method, ...(params ? { params } : {}) });
  }

  /** initialize + initialized notification, returns the init result. */
  async connect(): Promise<any> {
    const result = await this.request('initialize', {
      protocolVersion: '2025-06-18',
      capabilities: {},
      clientInfo: { name: 'diviskit-e2e', version: '1.0.0' },
    });
    this.notify('notifications/initialized');
    return result;
  }

  async listTools(): Promise<{ name: string; [k: string]: any }[]> {
    const res = await this.request<{ tools: { name: string }[] }>('tools/list');
    return res.tools;
  }

  async callTool(name: string, args: Record<string, unknown> = {}): Promise<any> {
    const res = await this.request<{ content: { type: string; text: string }[]; isError?: boolean }>(
      'tools/call',
      { name, arguments: args },
    );
    const text = res.content?.find((c) => c.type === 'text')?.text;
    try {
      return { raw: res, envelope: text ? JSON.parse(text) : undefined };
    } catch {
      return { raw: res, envelope: undefined };
    }
  }

  close() {
    this.child.kill('SIGTERM');
  }
}
