  function openModal() {
        document.getElementById('passengerModal').classList.add('active');
    }
    function closeModal() {
        document.getElementById('passengerModal').classList.remove('active');
    }
    // Close when clicking outside modal box
    document.getElementById('passengerModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });