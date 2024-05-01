import {authentication, apiRequest, ApiMethod} from '$core/libs/requests'

export async function auth(credentials: FormData): Promise<void> {
  const response: Response = await apiRequest('auth', ApiMethod.POST, credentials)

  // All is OK
  if (response.ok && response.headers.has(authentication.TokenHeader)) return;

  if (response.status === 401) throw Error('Neplatné přihlašovací údaje')

  console.debug({response})
  throw Error('Neočekávaný stav')
}
