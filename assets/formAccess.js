isChecked = document.getElementById('restrict-access');
input = document.getElementById('restrict-access-input');
function restrictAccessIsCheked() {
    if (isChecked.checked) {
        input.required = true;
    } else {
        input.required = false;
    }
}
restrictAccessIsCheked();
isChecked.addEventListener('change', function (event) {
    restrictAccessIsCheked();
});
