function activateModal() {
    // initialize modal element
    var modalEl = document.getElementById('create_card_form').cloneNode(true);
    modalEl.style.margin = '30vh auto';
    modalEl.style.backgroundColor = '#fff';
    modalEl.style.display = 'block';
    modalEl.style.float = 'inherit';

    // show modal
    mui.overlay('on', modalEl);
}

jQuery(document).ready(function ($) {

});