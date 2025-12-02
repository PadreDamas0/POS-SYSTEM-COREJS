// Authentication management
const Database = {
  getUserByUsername(username) {
    // Mock implementation for demonstration purposes
    return { username: "test", password: "hashed_password", role: "admin", status: "active" }
  },

  hashPassword(password) {
    // Mock implementation for demonstration purposes
    return "hashed_password"
  },
}

const Auth = {
  getCurrentUser() {
    const user = sessionStorage.getItem("current_user")
    return user ? JSON.parse(user) : null
  },

  setCurrentUser(user) {
    sessionStorage.setItem("current_user", JSON.stringify(user))
  },

  login(username, password) {
    const user = Database.getUserByUsername(username)
    if (!user) return { success: false, message: "User not found" }

    const hashedPassword = Database.hashPassword(password)
    if (user.password !== hashedPassword) {
      return { success: false, message: "Invalid password" }
    }

    if (user.status !== "active") {
      return { success: false, message: "User account is suspended" }
    }

    this.setCurrentUser(user)
    return { success: true, user }
  },

  logout() {
    sessionStorage.removeItem("current_user")
  },

  isLoggedIn() {
    return this.getCurrentUser() !== null
  },

  isSuperadmin() {
    const user = this.getCurrentUser()
    return user && user.role === "superadmin"
  },

  isAdmin() {
    const user = this.getCurrentUser()
    return user && (user.role === "admin" || user.role === "superadmin")
  },
}
