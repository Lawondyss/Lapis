import {PUBLIC_API_BASE_URL} from '$env/static/public'
import {tokenStore} from '$core/libs/stores'
import {user, type UserIdentity} from '$core/libs/user.svelte'
import * as jwt from '$core/libs/jwt'

class Authentication {
  readonly TokenHeader: string = 'X-Auth-Token'
  readonly AuthHeader: string = 'Authorization'

  createBearer(token: string): string {
    return `Bearer ${token}`
  }
}

export const authentication: Authentication = new Authentication


export enum ApiMethod {
  GET = 'GET',
  POST = 'POST',
  PUT = 'PUT',
  DELETE = 'DELETE',
}

export type ResponseData = {
  status: 'OK' | 'ERROR',
  code: number,
  message?: string,
  result?: object | object[],
  errors?: string[],
}

export async function apiRequest(endpoint: string, method: ApiMethod, body: any = null): Promise<Response> {
  const options: {
    method: string,
    headers: Record<string, string>,
    body?: string | FormData,
  } = {
    method,
    headers: {},
  }

  // Transforming body for request
  if (body !== null) {
    if (body instanceof FormData) {
      options.body = body
    } else {
      options.body = JSON.stringify(body)
      options.headers['Content-Type'] = 'application/json'
    }
  }

  // If a local token exists, then sends it in headers
  const tokenLocal: string | null = tokenStore.read()
  if (tokenLocal !== null) options.headers[authentication.AuthHeader] = authentication.createBearer(tokenLocal)

  const response: Response = await fetch(new URL(endpoint, PUBLIC_API_BASE_URL), options)

  if (response.status === 401) {
    user.logout()
    tokenStore.delete()
  }

  // If a token in the headers exists, then stores it on local and create user identity
  const tokenHeader: string | null = response.headers.get(authentication.TokenHeader)
  if (tokenHeader) {
    tokenStore.write(tokenHeader)
    const payload = jwt.decodePayload(tokenHeader)
    user.login(payload.data)
  }

  return response
}


export async function api<T>(endpoint: string, method: ApiMethod = ApiMethod.GET, body: any = null): Promise<T | null> {
  const response: Response = await apiRequest(endpoint, method, body);
  const data: ResponseData | null = await response.json()

  return data?.result as T | null
}