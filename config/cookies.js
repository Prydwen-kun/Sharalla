class Cookie {
  static setAuthCookie(auth_token) {
    document.cookie('S_token', auth_token, {
      maxAge: 36000000,
      httpOnly: true,
    })
  }

  static getAuthCookie() {
    const name = 'S_token'
    const decodedCookie = decodeURIComponent(document.cookie)
    const cookieArray = decodedCookie.split(';')
    for (let i = 0; i < cookieArray.length; i++) {
      let cookie = cookieArray[i].trim()
      if (cookie.indexOf(name) === 0) {
        return cookie.substring(name.length, cookie.length)
      }
    }
    return null
  }
}
export default Cookie
