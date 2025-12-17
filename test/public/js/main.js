function searchPlate() {
    const plateInput = document.getElementById('search_plate');
    const plate = plateInput.value.trim();
    const resultDiv = document.getElementById('search_result');
    const hiddenInput = document.getElementById('blocked_plate_input'); // The hidden box
    const submitBtn = document.getElementById('submit_btn'); // The disabled button

    if (!plate) {
        resultDiv.innerHTML = '<span style="color:var(--error-color)">Please enter a plate number</span>';
        return;
    }

    const formData = new FormData();
    formData.append('plate', plate);

    fetch(URLROOT + '/report/search', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'found') {
            // 1. Show Success Message
            resultDiv.innerHTML = `<span style="color:var(--success-color); font-weight:bold;">Found: ${data.model} (${data.color})</span>`;
            
            // 2. Fill the hidden input so the form can send the data
            if (hiddenInput) {
                hiddenInput.value = plate; 
            }

            // 3. ENABLE the Submit Button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
                submitBtn.style.cursor = "pointer";
            }

        } else {
            // 1. Show Error Message
            resultDiv.innerHTML = '<span style="color:var(--error-color)">Vehicle not found. Cannot report.</span>';
            
            // 2. Clear the hidden input
            if (hiddenInput) {
                hiddenInput.value = '';
            }

            // 3. DISABLE the Submit Button (Prevent false reports)
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = "0.5";
                submitBtn.style.cursor = "not-allowed";
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultDiv.innerHTML = '<span style="color:var(--error-color)">System Error</span>';
    });
}