//----------------------- Live search in _loaded_ tag cloud -------------------------------
function toggleSearchDropDown() {
    document.getElementById("searchDD").classList.toggle("show");
}

function menuItemClickHandler() {
    let searchField = document.getElementById("searchField");
    searchField.value = this.innerText;
    document.getElementById("searchDD").classList.remove("show");
}

//This function fills drop down menu with real data, taken from JSON composed in PHP - ALL keywords from articles table
//This function run only once - when DOM is ready - see EventListener below
function searchMenuInitialization() {
    let searchDropDownBlock;
    //let filterBase = JSON.parse('<?= $keywordsJSON ?>');
    let hiddenJSON = document.getElementById("hiddenJSON").innerText;
    let filterBase = JSON.parse(hiddenJSON);
    for (let i in filterBase) {
        if (filterBase.hasOwnProperty(i)) {
            let a = document.createElement("a"); //create <a></a> element
            let linkText = document.createTextNode(filterBase[i]); //
            a.appendChild(linkText); //add text (real tagname) as child to <a></a>
            //a.href = "http://example.com?search="+filterBase[i]; //set URL to <a></a>
            a.id = "linkID"+i;
            a.addEventListener("click", menuItemClickHandler, false);
            searchDropDownBlock = document.getElementById("searchDD"); //find drop-down menu <div>
            searchDropDownBlock.appendChild(a); //add link <a>...</a> as child to given <div> block
        }
    }
    let searchField = document.getElementById("searchField");
    searchField.addEventListener("click", toggleSearchDropDown, false);
    searchField.addEventListener("keyup", checkAndFilterSearch, false);
}

//This function runs every time user release button on search field
function checkAndFilterSearch() {
    document.getElementById("searchDD").classList.add("show"); //Just to be sure this block always open when user typing
    let userInput = document.getElementById("searchField").value.toUpperCase();
    let searchDropDownBlock = document.getElementById("searchDD"); //find drop-down menu <div>
    let links = searchDropDownBlock.getElementsByTagName("a");
    for (let i=0; i<links.length; i++) {
        if (links[i].innerHTML.toUpperCase().indexOf(userInput) > -1) {
            links[i].style.display = "";
        } else {
            links[i].style.display = "none";
        }
    }

    //If user pressed ESC key, hide the dropdown block:
    let event = window.event;
    if(event.keyCode === 27) {
        document.getElementById("searchDD").classList.remove("show");
    }
}

window.onload = searchMenuInitialization;
//document.addEventListener("DOMContentLoaded", searchMenuInitialization);