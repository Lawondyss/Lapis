export class LocalStore {
  private readonly prefix: string
  
  constructor(name: string) {
    this.prefix = name
  }
  
  try(name: string): any {
    try {
      return this.get(name)
    } catch (Error) {
      return null
    }
  }
  
  get(name: string): any {
    const value = localStorage.getItem(this.key(name))
    
    if (value === null) throw Error(`Value '${name}' is not set`)
    
    return JSON.parse(value)
  }
  
  set(name: string, value: any): void {
    localStorage.setItem(this.key(name), JSON.stringify(value))
  }

  remove(name: string): void {
    localStorage.removeItem(this.key(name))
  }
  
  private key(name: string): string {
    return `${this.prefix}:${name}`
  }
}


class TokenStore {
  private readonly TokenKey: string = 'token'
  private readonly store: LocalStore
  
  constructor() {
    this.store = new LocalStore('auth')
  }
  
  read(): string | null {
    return this.store.try(this.TokenKey)
  }
  
  write(token: string): void {
    this.store.set(this.TokenKey, token)
  }

  delete(): void {
    this.store.remove(this.TokenKey)
  }
}

export const tokenStore = new TokenStore
