import type {UserIdentity} from '$core/libs/user'

export type Payload = {
  exp: number,
  data: UserIdentity
}

export function decodePayload(token: string): Payload {
  const payload = token.split('.')[1];
  const base64 = payload.replaceAll('-', '+').replaceAll('_', '/');
  const json = atob(base64)

  return JSON.parse(json)
}