// feedback.js
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("feedbackForm");
  form.addEventListener("submit", function (e) {
    const rating = form.querySelector('input[name="rating"]:checked');
    const comment = form.comment.value.trim();

    if (!rating) {
      alert("Vui lòng chọn số sao.");
      e.preventDefault();
      return;
    }
    if (comment.length < 5) {
      alert("Bình luận quá ngắn (ít nhất 5 ký tự).");
      e.preventDefault();
      return;
    }
    // Nếu muốn dùng AJAX thay thế submit, hãy uncomment code dưới:
    /*
    e.preventDefault();
    const formData = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(res => res.text())
    .then(() => {
      alert('Cảm ơn bạn đã đánh giá!');
      window.location.href = document.referrer || 'index.php';
    })
    .catch(() => {
      alert('Có lỗi, vui lòng thử lại.');
    });
    */
  });
});
