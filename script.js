let cart = [];

$(document).ready(function () {
  $('.addBtn').click(function () {
    const card = $(this).closest('.item-card');
    const name = card.data('name');
    const price = parseInt(card.data('price'));

    const existing = cart.find(item => item.name === name);
    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ name, price, qty: 1 });
    }

    renderCart();
  });

  $('#payBtn').click(function () {
    const total = parseInt($('#cartTotal').text());
    const payment = parseInt($('#paymentInput').val());

    if (isNaN(payment) || payment < total) {
      alert("Not enough payment!");
      return;
    }

    const change = payment - total;

    const receiptHTML = cart.map(item => {
      return `<div>${item.name} x${item.qty} - ₱${item.qty * item.price}</div>`;
    }).join('');

    $('#receiptContent').html(`
      ${receiptHTML}
      <hr>
      <div><strong>Total:</strong> ₱${total}</div>
      <div><strong>Paid:</strong> ₱${payment}</div>
      <div><strong>Change:</strong> ₱${change}</div>
    `);

    $('#receipt').slideDown();
    cart = [];
    renderCart();
  });
});

function renderCart() {
  const $cartItems = $('#cartItems');
  const $cartTotal = $('#cartTotal');
  $cartItems.empty();
  let total = 0;

  cart.forEach(item => {
    const itemTotal = item.qty * item.price;
    total += itemTotal;
    $cartItems.append(`<li>${item.name} x${item.qty} - ₱${itemTotal}</li>`);
  });

  $cartTotal.text(total);
}
