function confirmCheckout(total, balance) {
  if (parseFloat(balance) < parseFloat(total)) {
    alert("Insufficient balance. Please add more balance.");
    return false;
  }
  return confirm("Confirm purchase? Total: " + total);
}

