 document.querySelectorAll('.btn-follow').forEach(btn => {
            btn.addEventListener('click', function() {
                const commandeId = this.getAttribute('data-commande-id');
                const followMenu = document.getElementById('followMenu-' + commandeId);
                const overlay = document.getElementById('overlay-' + commandeId);
                
                followMenu.classList.toggle('open');
                overlay.classList.toggle('visible');
            });
        });

// OVERLAY

        document.querySelectorAll('.overlay').forEach(overlay => {
            overlay.addEventListener('click', function() {
                const commandeId = this.id.replace('overlay-', '');
                const followMenu = document.getElementById('followMenu-' + commandeId);
                
                followMenu.classList.remove('open');
                this.classList.remove('visible');
            });
        });
