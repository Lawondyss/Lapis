import {get, writable, type Writable} from 'svelte/store'
import {LocalStore} from '$core/libs/stores'


export type UserId = string | number;

export type UserIdentity = {
  id: UserId | null,
  name: string,
  roles: string[],
}

class User {
  private readonly StoreKey: string = 'identity'
  private readonly localStore: LocalStore
  private readonly svelteStore: Writable<UserIdentity | null>

  constructor() {
    this.localStore = new LocalStore('user')
    this.svelteStore = writable(this.localStore.try(this.StoreKey))

    this.svelteStore.subscribe((identity: UserIdentity | null): void => {
      identity
        ? this.localStore.set(this.StoreKey, identity)
        : this.localStore.remove(this.StoreKey)
    })
  }

  login(identity: UserIdentity): void {
    this.svelteStore.set(identity)
  }

  logout(): void {
    this.svelteStore.set(null)
  }

  onAuth(callback: (user: UserIdentity | null) => void): void {
    this.svelteStore.subscribe(callback)
  }

  isLoggedIn(): boolean {
    return !!get(this.svelteStore)
  }
}

export const user = new User