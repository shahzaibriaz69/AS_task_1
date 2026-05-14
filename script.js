    function openModal()  { document.getElementById('passengerModal').classList.add('active'); }
    function closeModal() { document.getElementById('passengerModal').classList.remove('active'); }
    document.getElementById('passengerModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });