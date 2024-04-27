import {LocalStore, tokenStore} from '$core/libs/stores'


export type UserId = string | number;

export type UserIdentity = {
  id: UserId | null,
  name: string,
  roles: string[],
}

class User {
  private readonly StoreKey: string = 'identity'

  private readonly localStore: LocalStore = new LocalStore('user')
  private userIdentity: UserIdentity | null = $state(null)

  constructor() {
    this.userIdentity = this.localStore.try(this.StoreKey)
    $inspect(this.userIdentity)
  }

  get identity(): UserIdentity {
    if (!this.userIdentity) throw Error('User is not logged')

    return $state.snapshot(this.userIdentity)
  }

  login(identity: UserIdentity): void {
    this.userIdentity = identity
    this.localStore.set(this.StoreKey, this.userIdentity)
  }

  logout(): void {
    this.userIdentity = null
    this.localStore.remove(this.StoreKey)
    tokenStore.delete()
  }

  isLoggedIn(): boolean {
    return this.userIdentity !== null
  }

  hasRole(role: string): boolean {
    return this.identity.roles.includes(role)
  }
}

export const user = new User
