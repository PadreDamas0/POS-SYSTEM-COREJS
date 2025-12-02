// Import or declare the Auth and Database variables before using them
const Auth = {
  isLoggedIn: () => false,
  getCurrentUser: () => ({ role: "cashier" }),
  login: (username, password) => ({ success: true }),
  logout: () => {},
}

const Database = {
  getAllUsers: () => [],
  getOrders: () => [],
  updateUserStatus: (userId, status) => {},
  getAllProducts: () => [],
  createOrder: (cart, total, payment, userId) => ({
    items: cart,
    total_amount: total,
    payment_amount: payment,
    order_number: "12345",
    date_added: new Date(),
  }),
}

const App = {
  currentPage: "login",
  cart: [],

  init() {
    if (Auth.isLoggedIn()) {
      const user = Auth.getCurrentUser()
      if (user.role === "superadmin") {
        this.showAdminDashboard()
      } else {
        this.showPOS()
      }
    } else {
      this.showLogin()
    }
  },

  showLogin() {
    this.currentPage = "login"
    document.getElementById("app").innerHTML = `
            <div class="login-container">
                <div class="login-box">
                    <h1>DAMBALASEK™ POS</h1>
                    <form id="loginForm">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="password" required>
                        </div>
                        <button type="submit" class="login-btn">Login</button>
                        <p class="demo-info">Demo: superadmin / superadmin123 or cashier1 / cashier123</p>
                    </form>
                </div>
            </div>
        `

    document.getElementById("loginForm").addEventListener("submit", (e) => {
      e.preventDefault()
      const username = document.getElementById("username").value
      const password = document.getElementById("password").value

      const result = Auth.login(username, password)
      if (result.success) {
        this.init()
      } else {
        alert(result.message)
      }
    })
  },

  showAdminDashboard() {
    this.currentPage = "admin"
    const users = Database.getAllUsers()
    const orders = Database.getOrders()
    const totalRevenue = orders.reduce((sum, order) => sum + order.total_amount, 0)
    const adminCount = users.length
    const totalOrders = orders.length

    document.getElementById("app").innerHTML = `
            <div class="admin-container">
                <div class="header">
                    <h1>Superadmin Dashboard</h1>
                    <div class="nav-buttons">
                        <button onclick="App.showManageUsers()" class="nav-btn">Manage Users</button>
                        <button onclick="App.showManageProducts()" class="nav-btn">Manage Products</button>
                        <button onclick="App.showReports()" class="nav-btn">Reports</button>
                        <button onclick="App.logout()" class="nav-btn logout-btn">Logout</button>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="card">
                        <h3>Total Admins</h3>
                        <div class="card-value">${adminCount}</div>
                    </div>
                    <div class="card">
                        <h3>Total Orders</h3>
                        <div class="card-value">${totalOrders}</div>
                    </div>
                    <div class="card">
                        <h3>Total Revenue</h3>
                        <div class="card-value">₱${totalRevenue.toFixed(2)}</div>
                    </div>
                </div>

                <div class="users-section">
                    <h2>Registered Admins</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${users
                              .map(
                                (user) => `
                                <tr>
                                    <td>${user.id}</td>
                                    <td>${user.username}</td>
                                    <td>${user.email}</td>
                                    <td class="status-${user.status}">${user.status.toUpperCase()}</td>
                                    <td>${new Date(user.date_added).toLocaleDateString()}</td>
                                    <td>
                                        <div class="action-buttons">
                                            ${
                                              user.status === "active"
                                                ? `
                                                <button class="btn-small btn-suspend" onclick="App.suspendUser(${user.id})">Suspend</button>
                                            `
                                                : `
                                                <button class="btn-small btn-activate" onclick="App.activateUser(${user.id})">Activate</button>
                                            `
                                            }
                                        </div>
                                    </td>
                                </tr>
                            `,
                              )
                              .join("")}
                        </tbody>
                    </table>
                </div>
            </div>
        `
  },

  showManageUsers() {
    alert("User Management feature coming soon")
    this.showAdminDashboard()
  },

  showManageProducts() {
    alert("Product Management feature coming soon")
    this.showAdminDashboard()
  },

  showReports() {
    this.currentPage = "reports"
    const orders = Database.getOrders()
    const totalRevenue = orders.reduce((sum, order) => sum + order.total_amount, 0)

    document.getElementById("app").innerHTML = `
            <div class="admin-container">
                <div class="header">
                    <h1>Reports</h1>
                    <button onclick="App.showAdminDashboard()" class="nav-btn">Back to Dashboard</button>
                </div>

                <div class="dashboard-grid">
                    <div class="card">
                        <h3>Total Orders</h3>
                        <div class="card-value">${orders.length}</div>
                    </div>
                    <div class="card">
                        <h3>Total Revenue</h3>
                        <div class="card-value">₱${totalRevenue.toFixed(2)}</div>
                    </div>
                </div>

                <div class="users-section">
                    <h2>Order History</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Total Amount</th>
                                <th>Payment</th>
                                <th>Change</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${orders
                              .map(
                                (order) => `
                                <tr>
                                    <td>${order.order_number}</td>
                                    <td>₱${order.total_amount.toFixed(2)}</td>
                                    <td>₱${order.payment_amount.toFixed(2)}</td>
                                    <td>₱${order.change_amount.toFixed(2)}</td>
                                    <td>${new Date(order.date_added).toLocaleDateString()}</td>
                                </tr>
                            `,
                              )
                              .join("")}
                        </tbody>
                    </table>
                </div>
            </div>
        `
  },

  suspendUser(userId) {
    if (confirm("Suspend this user?")) {
      Database.updateUserStatus(userId, "suspended")
      this.showAdminDashboard()
    }
  },

  activateUser(userId) {
    if (confirm("Activate this user?")) {
      Database.updateUserStatus(userId, "active")
      this.showAdminDashboard()
    }
  },

  showPOS() {
    this.currentPage = "pos"
    const user = Auth.getCurrentUser()
    const products = Database.getAllProducts()

    document.getElementById("app").innerHTML = `
            <div class="pos-container">
                <div class="header">
                    <h1>DAMBALASEK™ POS</h1>
                    <div class="user-info">
                        <span>Welcome, ${user.username}</span>
                        <form onsubmit="App.logout(); return false;" style="display: inline;">
                            <button type="submit" class="logout-btn">Logout</button>
                        </form>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-header">
                        <h2>Menu Items</h2>
                    </div>
                    <div class="menu-container" id="menuContainer">
                        ${products
                          .map(
                            (product) => `
                            <div class="item-card" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">
                                <img src="${product.image_path}" alt="${product.name}" onerror="this.style.display='none'">
                                <h4>${product.name}</h4>
                                <p>₱${product.price.toFixed(2)}</p>
                                <button type="button" class="addBtn" onclick="App.addToCart(${product.id}, '${product.name}', ${product.price})">Add to Cart</button>
                            </div>
                        `,
                          )
                          .join("")}
                    </div>
                </div>

                <div class="cart-section">
                    <div class="cart-box">
                        <h3>Your Cart</h3>
                        <ul id="cartItems"></ul>
                        <div class="cart-total">₱<span id="cartTotal">0.00</span></div>
                        <button class="clear-cart-btn" onclick="App.clearCart()">Clear Cart</button>
                    </div>

                    <div class="cart-box payment-section">
                        <h3 style="margin: 0 0 10px 0; color: #b30000;">Payment</h3>
                        <input type="number" id="paymentInput" placeholder="Enter amount" step="0.01" min="0">
                        <button id="payBtn" onclick="App.processOrder()">Complete Order & Pay</button>
                    </div>
                </div>
            </div>

            <div id="receiptModal" class="receipt-modal">
                <div class="receipt-content">
                    <h2>Order Receipt</h2>
                    <div id="receiptContent"></div>
                    <button class="close-receipt" onclick="App.closeReceipt()">Print & Close</button>
                </div>
            </div>
        `

    this.renderCart()
  },

  addToCart(productId, name, price) {
    const existing = this.cart.find((item) => item.id === productId)
    if (existing) {
      existing.qty += 1
    } else {
      this.cart.push({ id: productId, name, price, qty: 1 })
    }
    this.renderCart()
  },

  renderCart() {
    const cartItemsEl = document.getElementById("cartItems")
    const cartTotalEl = document.getElementById("cartTotal")

    if (!cartItemsEl) return

    cartItemsEl.innerHTML = ""
    let total = 0

    this.cart.forEach((item, index) => {
      const itemTotal = item.qty * item.price
      total += itemTotal

      const li = document.createElement("li")
      li.innerHTML = `
                <div>
                    <strong>${item.name}</strong><br>
                    x${item.qty} @ ₱${item.price.toFixed(2)} = ₱${itemTotal.toFixed(2)}
                </div>
                <button class="cart-item-remove" onclick="App.removeFromCart(${index})">Remove</button>
            `
      cartItemsEl.appendChild(li)
    })

    if (cartTotalEl) {
      cartTotalEl.textContent = total.toFixed(2)
    }
  },

  removeFromCart(index) {
    this.cart.splice(index, 1)
    this.renderCart()
  },

  clearCart() {
    this.cart = []
    this.renderCart()
    const paymentInput = document.getElementById("paymentInput")
    if (paymentInput) paymentInput.value = ""
  },

  processOrder() {
    if (this.cart.length === 0) {
      alert("Cart is empty")
      return
    }

    const total = Number.parseFloat(document.getElementById("cartTotal").textContent)
    const payment = Number.parseFloat(document.getElementById("paymentInput").value)

    if (isNaN(payment) || payment < total) {
      alert("Not enough payment!")
      return
    }

    const user = Auth.getCurrentUser()
    const order = Database.createOrder(this.cart, total, payment, user.id)
    const change = payment - total

    this.showReceipt(order, change)
    this.clearCart()
  },

  showReceipt(order, change) {
    let receiptHTML = '<div class="receipt-items">'
    order.items.forEach((item) => {
      receiptHTML += `<div class="receipt-item"><span>${item.name} x${item.qty}</span><span>₱${(item.qty * item.price).toFixed(2)}</span></div>`
    })
    receiptHTML += "</div>"
    receiptHTML += `<div class="receipt-summary">
            <div><span>Order #:</span><span>${order.order_number}</span></div>
            <div><span>Subtotal:</span><span>₱${order.total_amount.toFixed(2)}</span></div>
            <div><span>Payment:</span><span>₱${order.payment_amount.toFixed(2)}</span></div>
            <div style="font-size: 1.1rem; border-top: 1px solid #ddd; padding-top: 10px;"><span>Change:</span><span>₱${change.toFixed(2)}</span></div>
        </div>`

    document.getElementById("receiptContent").innerHTML = receiptHTML
    document.getElementById("receiptModal").style.display = "block"
  },

  closeReceipt() {
    document.getElementById("receiptModal").style.display = "none"
  },

  logout() {
    Auth.logout()
    this.init()
  },
}

// Initialize app on page load
document.addEventListener("DOMContentLoaded", () => {
  App.init()
})
