// Database management using localStorage
const Database = {
  // Initialize database with default data
  init() {
    if (!localStorage.getItem("db_initialized")) {
      this.initializeUsers()
      this.initializeProducts()
      this.initializeOrders()
      localStorage.setItem("db_initialized", "true")
    }
  },

  initializeUsers() {
    const users = [
      {
        id: 1,
        username: "superadmin",
        password: this.hashPassword("superadmin123"),
        email: "superadmin@dambalasek.com",
        role: "superadmin",
        status: "active",
        date_added: new Date().toISOString(),
      },
      {
        id: 2,
        username: "cashier1",
        password: this.hashPassword("cashier123"),
        email: "cashier1@dambalasek.com",
        role: "admin",
        status: "active",
        date_added: new Date().toISOString(),
      },
    ]
    localStorage.setItem("users", JSON.stringify(users))
  },

  initializeProducts() {
    const products = [
      {
        id: 1,
        name: "Menudong Imus",
        price: 60,
        description: "Traditional Imus Menudo",
        image_path: "https://getrecipe.org/wp-content/uploads/2023/01/Copy-of-Pork-Menudo-1.png",
        status: "active",
      },
      {
        id: 2,
        name: "Gentri Valenciana",
        price: 70,
        description: "Spanish-style rice dish",
        image_path:
          "https://i0.wp.com/diyaryomilenyonews.com/wp-content/uploads/2020/08/arroz1.jpg?fit=959%2C640&ssl=1",
        status: "active",
      },
      {
        id: 3,
        name: "Bacalao",
        price: 65,
        description: "Salted codfish dish",
        image_path:
          "https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiULHGkCcEUnJsagw2EMOeuD0C8VqqTd97BA7x1DCtDVEBheYMunLgl_5rngYtz5lUOVfollpj1UdaXoULF_4q1_1AyIHuL67_Gm_KHXsU_yVQI9fLQhzG1l68ssf8QVZxqBikgOROiXWOS/s1600/bacalao.jpg",
        status: "active",
      },
      {
        id: 4,
        name: "Kilawin Cavite",
        price: 55,
        description: "Cavite-style kilawin",
        image_path: "https://www.ikot.ph/wp-content/uploads/2023/07/g.png",
        status: "active",
      },
      {
        id: 5,
        name: "Pancit Puso",
        price: 50,
        description: "Heart-shaped noodles",
        image_path: "https://i.ytimg.com/vi/jrPy5dBYCeE/hq720.jpg",
        status: "active",
      },
      {
        id: 6,
        name: "Pancit Luglog",
        price: 50,
        description: "Quirino road-style pancit",
        image_path: "https://images.yummy.ph/yummy/uploads/2015/11/pancit-luglog-645-1.jpg",
        status: "active",
      },
      {
        id: 7,
        name: "Pochero con Sarsa",
        price: 75,
        description: "Pochero with sauce",
        image_path: "https://i.ytimg.com/vi/gbmeLKie17k/maxresdefault.jpg",
        status: "active",
      },
      {
        id: 8,
        name: "Calandracas",
        price: 65,
        description: "Traditional calandracas",
        image_path: "https://images.yummy.ph/yummy/uploads/2009/11/calandracas.jpg",
        status: "active",
      },
    ]
    localStorage.setItem("products", JSON.stringify(products))
  },

  initializeOrders() {
    localStorage.setItem("orders", JSON.stringify([]))
  },

  // User methods
  getUser(id) {
    const users = JSON.parse(localStorage.getItem("users") || "[]")
    return users.find((u) => u.id === id)
  },

  getUserByUsername(username) {
    const users = JSON.parse(localStorage.getItem("users") || "[]")
    return users.find((u) => u.username === username)
  },

  getAllUsers() {
    const users = JSON.parse(localStorage.getItem("users") || "[]")
    return users.filter((u) => u.role === "admin")
  },

  updateUserStatus(userId, status) {
    const users = JSON.parse(localStorage.getItem("users") || "[]")
    const user = users.find((u) => u.id === userId)
    if (user) {
      user.status = status
      localStorage.setItem("users", JSON.stringify(users))
      return true
    }
    return false
  },

  // Product methods
  getProduct(id) {
    const products = JSON.parse(localStorage.getItem("products") || "[]")
    return products.find((p) => p.id === id)
  },

  getAllProducts() {
    const products = JSON.parse(localStorage.getItem("products") || "[]")
    return products.filter((p) => p.status === "active")
  },

  // Order methods
  createOrder(items, totalAmount, paymentAmount, userId) {
    const orders = JSON.parse(localStorage.getItem("orders") || "[]")
    const orderNumber =
      "ORD-" +
      new Date()
        .toISOString()
        .replace(/[^0-9]/g, "")
        .slice(0, 14)
    const changeAmount = paymentAmount - totalAmount

    const order = {
      id: orders.length + 1,
      order_number: orderNumber,
      items: items,
      total_amount: totalAmount,
      payment_amount: paymentAmount,
      change_amount: changeAmount,
      created_by: userId,
      date_added: new Date().toISOString(),
    }

    orders.push(order)
    localStorage.setItem("orders", JSON.stringify(orders))
    return order
  },

  getOrders(dateStart = null, dateEnd = null) {
    let orders = JSON.parse(localStorage.getItem("orders") || "[]")

    if (dateStart) {
      orders = orders.filter((o) => new Date(o.date_added).toDateString() >= new Date(dateStart).toDateString())
    }

    if (dateEnd) {
      orders = orders.filter((o) => new Date(o.date_added).toDateString() <= new Date(dateEnd).toDateString())
    }

    return orders.sort((a, b) => new Date(b.date_added) - new Date(a.date_added))
  },

  hashPassword(password) {
    // Simple hash for demo purposes
    return btoa(password)
  },
}

// Initialize database on load
Database.init()
