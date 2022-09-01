function liveSearch() {
    // Locate the card elements
    let cards = document.querySelectorAll('.card')
    // Locate the search input
    let search_query = document.getElementById("searchbox").value;
    // Loop through the cards
    for (let i = 0; i < cards.length; i++) {
      // If the text is within the card...
        if(cards[i].innerText.toLowerCase()
        // ...and the text matches the search query...
        .includes(search_query.toLowerCase())) {
          // ...remove the `.is-hidden` class.
        cards[i].classList.remove("is-hidden");
        } else {
        // Otherwise, add the class.
        cards[i].classList.add("is-hidden");
        }
    }
}