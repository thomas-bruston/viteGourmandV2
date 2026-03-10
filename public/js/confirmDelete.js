// CONFIRMATION AVANT SUPRESSION

document.addEventListener('DOMContentLoaded', function() {
    const btnSupprimer = document.querySelector('.btn-supprimer');
    
    if (btnSupprimer) {
        btnSupprimer.addEventListener('click', function() {
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce menu ?\n\nCette action est irréversible !')) {
                
                document.getElementById('deleteForm').submit();
            }
        });
    }
});