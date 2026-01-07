//برای جستجو در جدول
document.getElementById('search-input').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});


// برای مخفی کردن لودینگ بعد از لود کامل صفحه
window.addEventListener('load', function() {
    const loadingElement = document.getElementById('loading');
    loadingElement.style.opacity = '0';
    setTimeout(function() {
        loadingElement.style.display = 'none';
    }, 500); // زمان fade out بعد از opacity=0
});