document.addEventListener('DOMContentLoaded', function() {
    // Register form validation
    const registerForm = document.querySelector('#register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
            }
        });
    }

    // Add/Edit game form validation
    const gameForms = document.querySelectorAll('#add-game-form, #edit-game-form');
    gameForms.forEach(form => {
        const platformSelect = form.querySelector('#platform');
        const platformOther = form.querySelector('#platform_other');
        const regionSelect = form.querySelector('#region');
        const regionOther = form.querySelector('#region_other');
        const conditionSelect = form.querySelector('#condition');
        const conditionOther = form.querySelector('#condition_other');

        // Platform "Other" toggle
        if (platformSelect && platformOther) {
            platformSelect.addEventListener('change', function() {
                platformOther.style.display = this.value === 'Other' ? 'block' : 'none';
                platformOther.required = this.value === 'Other';
                if (!platformOther.required) {
                    platformOther.value = '';
                }
            });
        };
        // Region "Other" toggle
        if (regionSelect && regionOther) {
            regionSelect.addEventListener('change', function() {
                regionOther.style.display = this.value === 'Other' ? 'block' : 'none';
                regionOther.required = this.value === 'Other';
                if (!regionOther.required) {
                    regionOther.value = '';
                }
            });
        };

        // Condition (Other" toggle
        if (conditionSelect && conditionOther) {
            conditionSelect.addEventListener('change', function() {
                conditionOther.style.display = this.value === 'Other' ? 'block' : 'none';
                conditionOther.required = this.value === 'Other';
                if (!conditionOther.required) {
                    conditionOther.value = '';
                }
            });
        };

        form.addEventListener('submit', function(e) {
            const title = form.querySelector('#title').value;
            const platform = platformSelect.value === 'Other' ? platformOther.value : platformSelect.value;
            const region = regionSelect.value === 'Other' ? regionOther.value : regionSelect.value;
            const condition = conditionSelect.value === 'Other' ? conditionOther.value : conditionSelect.value;
            const purchasePrice = form.querySelector('#purchase_price').value;
            const notes = form.querySelector('#notes').value;

            let errors = [];
            if (title.length > 100) {
                errors.push('Title must be 100 characters or less.');
            }
            if (platform.length > 50) {
                errors.push('Platform must be 50 characters or less.');
            }
            if (region.length > 50) {
                errors.push('Region must be 50 characters or less.');
            }
            if (condition.length > 50) {
                errors.push('Condition must be 50 characters or less.');
            }
            if (notes.length > 1000) {
                errors.push('Notes must be 1000 characters or less.');
            }
            if (purchasePrice && (isNaN(purchasePrice) || purchasePrice < 0)) {
                errors.push('Please enter a valid purchase price!');
            }
            if (platformSelect.value === 'Other' && !platformOther.value) {
                errors.push('Please enter a custom platform.');
            }
            if (regionSelect.value === 'Other' && !regionOther.value) {
                errors.push('Please enter a custom region.');
            }
            if (conditionSelect.value === 'Other' && !conditionOther.value) {
                errors.push('Please enter a custom condition.');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });
    });
});