function ajax(options){
    let {url, method, success, error, data} = options
    const args = {
        method: method || 'GET',
        headers: {
            'Content-Type':'application/json',
        },
        body: JSON.stringify(data),
    } 
    fetch(url,args)
    .then(response =>(response.ok)? response.json(): Promise.reject(response))
    .then(data=>success(data))
    .catch(e=>error(e))
}
const body = document.querySelector('body')
const collections = document.querySelector('#collections')
const template_collections = document.querySelector('#template-collection').content
const template_floor = document.querySelector('#template-floor').content
const form_search = document.querySelector('#form-search')
const floor_section = document.querySelector('#floor')
const main_container = document.querySelector('#main-container')
const secondary_container = document.querySelector('#form-container')
const modal1 = new bootstrap.Modal(document.querySelector('#modal1'))
const modal2 = new bootstrap.Modal(document.querySelector('#modal2'))
const watchlist = document.querySelector('#watchlist')
let linked = false
let portfolio_form
let attribute_counter = 0

const alert_success = document.querySelector('#linked-success')
const alert_warning = document.querySelector('#linked-warning')
const testAlertLink =  alert_success.querySelector('#send-test-alert')
const warningAlertLink = alert_warning.querySelector(".alert-link")
const reconfigure_telegram = alert_success.querySelector('#reconfigure-telegram')

document.addEventListener('DOMContentLoaded',e=>{
    showWatchList()
    getWatchList()
    getTopCollections()
    isTelegramLinked()
    testAlertLink.addEventListener('click',e=>{ 
        e.preventDefault()
        sendTestAlert()
    })
    reconfigure_telegram.addEventListener('click',e=>{
        e.preventDefault()
        modal1.show()
        addModalEvents()
    })
    warningAlertLink.addEventListener('click',e=>{
        e.preventDefault()
        modal1.show()
        addModalEvents()
    })

})


form_search.addEventListener('submit',e=>{
    e.preventDefault()
    if(isUrl(form_search.symbol.value)){
        const lastPos = form_search.symbol.value.lastIndexOf('/')
        form_search.symbol.value = form_search.symbol.value.slice(lastPos+1)
    }
    getCollections(form_search.symbol.value)


})

function getCollections(symbol){
    if(symbol){
        //If DB symbol does not exist, api call
        ajax({
            url: '/public/searchCollection',
            method: 'POST',
            success: json => {
                renderFloor(json)
                if(json.justInserted!=null){
                    insertCollection(json.symbol)
                }
                else{
                    //TO-DO Create a popup message
                }
                
  
            },
            error: msg => console.log(msg),
            data: {
                symbol: symbol,
            }
        })
    }
}

function insertCollection(symbol){
    let url = "/public/home"
    let formData = new FormData();
    formData.append('insertCollection', true);
    formData.append('symbol', symbol);
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(text=>{
        console.log(text)

    });

}
/* insertCollection('y00ts'); */

/* function renderCollections(json){
    collections.innerHTML = "";
    const fragment = document.createDocumentFragment()
    json.forEach(element =>{
        const clone = template_collections.cloneNode(true)
        const title = clone.querySelector('#collection-name')
        const img = clone.querySelector('img')
        const description = clone.querySelector('p')
        const a = clone.querySelector('a')

        title.textContent = element.name
        img.src = element.image
        description.textContent = element.description
        a.href = `https://magiceden.io/marketplace/${element.symbol}`
        fragment.appendChild(clone)
    })
    collections.appendChild(fragment)
} */

function renderFloor(json){
    const clone = template_floor.cloneNode(true)
    const name = clone.querySelector('.name')
    const listed = clone.querySelector('.listed')
    const avg = clone.querySelector('.avg-price')
    const volume = clone.querySelector('.volume')
    const floor = clone.querySelector('.floor')
    const img = clone.querySelector('img')
    const addToPortfolio = clone.querySelector('.addToPortfolio')
    const addToWatchList = clone.querySelector('.addToWatchlist')

    name.textContent = json.name
    listed.textContent = `Listed: ${json.listedCount}`
    if(json.avgPrice24hr){
        avg.textContent = `Average price: ${parseFloat(json.avgPrice24hr/1000000000).toFixed(2)} ◎` 
    }
    else{
        avg.remove()
    }
    volume.textContent = `Total volume: ${parseFloat(json.totalVolume/1000000000).toFixed(2)} ◎` || ""
    floor.innerHTML = `Floor price: <mark>${parseFloat(json.floorPrice/1000000000).toFixed(2)} ◎</mark>` || ""
    img.src = json.image
    
    collections.innerHTML=""
    floor_section.innerHTML=""

    addToWatchList.addEventListener('click',e=>{
        renderWatchListForm(json)
        const watchlist_form = document.querySelector('#form-watchlist')
        const submit = watchlist_form.querySelector('#submit-watchlist')
        const cancel = watchlist_form.querySelector('#cancel-form')

        submit.addEventListener('click',e=>{
            e.preventDefault()
            if(linked == false){
                modal1.show()
            }
            if(isFormEmpty(watchlist_form) == false){
                wlSubmit()
                document.querySelector('#watchlist-form-container').remove()
            }
        })
        cancel.addEventListener('click',e=>{
            e.preventDefault()
            document.querySelector('#watchlist-form-container').remove()
        })

        nav.querySelector('.active').classList.remove('active')
        nav.querySelector('#watchlist-link').classList.add('active')

    })
    addToPortfolio.addEventListener('click',e=>{
        showPortfolioForm()
        fillPortfolioForm(json)
        portfolio_form = document.querySelector('#portfolio-form')
        /* showPortfolio() */
        getPortfolio()
        nav.querySelector('.active').classList.remove('active')
        nav.querySelector('#portfolio-link').classList.add('active')



        
    })

    floor_section.appendChild(clone)
    addToWatchList.scrollIntoView();

}

//WATCHLIST FUNCTIONS

function addWatchListEvents(){

    const watchlist_form = document.querySelector('#watchlist-form')
    
    // Event Listener
    watchlist_form.addEventListener('click',e=>{
        e.preventDefault()
        if(e.target.name == 'activate'){
            changeAlertState(e.target.parentElement.dataset.id,0)
            getWatchList()
        }
        else if(e.target.name == 'turnOff'){
            changeAlertState(e.target.parentElement.dataset.id,1)
            getWatchList()
    
        }
        else if(e.target.name == 'remove'){
            removeAlert(e.target.parentElement.dataset.id)
            getWatchList()
    
        }
                
        }
    )

}


function changeAlertState(id, state){

    var url = '/public/home';
    var formData = new FormData();
    formData.append('id_alert', id);
    formData.append('active', state);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(function (response) {
        response.text();
    })
    .then(function (body) {
    });
}

function getWatchList(){
    var url = '/public/home';
    var formData = new FormData();
    formData.append('getWatchList', true);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.json())
    .then(json=>renderWatchListBody(json))
} 

//WatchList update event

function addAlert(symbol){
    var url = '/public/home';
    var formData = new FormData();
    formData.append('addAlert', true);
    formData.append('symbol', symbol);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(function (response) {
        return response.text();
    })
    .then(function (body) {
        getWatchList()
    });
}

function renderWatchListBody(json){
    document.querySelector('#watchlist-container').style.display = "block"
    const tbody_watchlist = document.querySelector('#tbody-watchlist')
    tbody_watchlist.innerHTML = ""
    document.querySelector('#portfolio-form')==null?"":document.querySelector('#portfolio-form').innerHTML=""
    const fragment = document.createDocumentFragment()
    const template_watchlist_row = document.querySelector('#template-row-wl').content
    json.forEach(element => {    
        const clone = template_watchlist_row.cloneNode(true)
        const a = clone.querySelector('a')
        const floor_price = clone.querySelector('.floor-price')
        const alert_price = clone.querySelector('.alert-price')
        const attributes = clone.querySelector('.attributes')
        const active = clone.querySelector('.active')
        const actions = clone.querySelector('.actions')
        const buttons = clone.querySelectorAll('button')

        var url = '/public/searchCollection';
        fetch(url, { 
            method: 'POST', 
            body: JSON.stringify({'symbol': element.symbol}), 
            headers: {
                'Content-Type':'application/json',
            },
        })
        .then(response=>response.json())
        .then(json=>{
            a.textContent = json.name
            
        })
        .catch(error => console.log(error))
        floor_price.textContent = (parseFloat(element.floorPrice)/1000000000).toFixed(2)
        a.href = `https://magiceden.io/marketplace/${element.symbol}`
        alert_price.value = (parseFloat(element.floor_price)/1000000000).toFixed(2)
        const attr_json = element.token_traits.split(',');

        console.log(attr_json);
        for (const item of attr_json) {
            let type = (item.slice(0,item.lastIndexOf('_')))
            type = type.slice(type.lastIndexOf('_')+1)

            let value = (item.slice(item.lastIndexOf('_')+1))
            
            attributes.innerHTML += `<u>${type.replaceAll("_"," ")}</u>: ${value}<br>`
        }

        if(element.active==1){
            let dateTimeParts = element.expiry_date.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
            dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
            const dateObject = new Date(...dateTimeParts);

            let local_date = new Date(dateObject.getTime() - dateObject.getTimezoneOffset()*60*1000);
            active.textContent = `Active until ${local_date.toLocaleString()}`
            buttons[0].disabled = true
        }
        else{
            active.textContent = 'Inactive'
            buttons[1].disabled = true
        }
        actions.dataset.id = element.id_alert
    
        //Add events for editing
        const edit = clone.querySelector('.edit')
        const check = clone.querySelector('.check')
        const icon_container = clone.querySelector('.icon-container')
        icon_container.addEventListener('click',e=>{
            //If user click on edit icon
            if(alert_price.disabled == true){
                alert_price.disabled = false
                edit.style.display = "none"
                check.style.display = "inline"
                alert_price.classList.remove('noborder')
                alert_price.focus()
            }
            //If user clicks on confirm icon
            else{
                alert_price.disabled = true
                edit.style.display = "inline"
                check.style.display = "none"
                alert_price.classList.add('noborder')
                changeAlertState(actions.dataset.id,0)
                setAlertPrice(actions.dataset.id, alert_price.value)

            }


        })

        fragment.appendChild(clone)
        
    });
    tbody_watchlist.appendChild(fragment)
    addBackgroundColor()
    const watchlist_form = document.querySelector('#watchlist-form')
    
    // Event Listener
    watchlist_form.addEventListener('click',e=>{
        e.preventDefault()
        if(e.target.name == 'activate'){
            changeAlertState(e.target.parentElement.dataset.id,0)
            getWatchList()
        }
        else if(e.target.name == 'turnOff'){
            changeAlertState(e.target.parentElement.dataset.id,1)
            getWatchList()
    
        }
        else if(e.target.name == 'remove'){
            removeAlert(e.target.parentElement.dataset.id)
            getWatchList()
    
        }
                
        }
    )

}

function showWatchList(){
    document.querySelector('#portfolio-table')? document.querySelector('#portfolio-table').classList.add('d-none'):""
    if(document.querySelector('#watchlist-container')!=null){
        document.querySelector('#watchlist-container').classList.remove('d-none')
        return
    }
    const template = document.querySelector('#template-watchlist-head').content
    const clone = template.cloneNode(true)
    main_container.appendChild(clone) 
}

function setAlertPrice(id_alert, alert_price){
    let url = '/public/home';
    let formData = new FormData();
    formData.append('setAlertPrice', true);
    formData.append('id_alert', id_alert);
    formData.append('alert_price', alert_price);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(function (body) {
        getWatchList()

    });
}

function removeAlert(id_alert){
    let url = '/public/home';
    let formData = new FormData();
    formData.append('removeAlert', true);
    formData.append('id_alert', id_alert);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(function (body) {
        getWatchList()

    });
}

function addBackgroundColor(){
    const tbody_watchlist = document.querySelector('#tbody-watchlist')
    const trs = tbody_watchlist.querySelectorAll('tr')
    trs.forEach(tr=>{
        if(tr.querySelector('.active').textContent.includes('Active')){
            tr.classList.add('bg-green')
        }
        if(tr.querySelector('.active').textContent == 'Inactive'){
            tr.classList.add('bg-yellow')
        }
 

    }) 
}

//PORTFOLIO

//Event
const nav = document.querySelector('nav')
const header = document.querySelector('header')
nav.addEventListener('click',e=>{
    if(e.target.id == 'watchlist-link'){
        showWatchList()
        getWatchList()
        nav.querySelector('.active').classList.remove('active')
        e.target.classList.add('active')
    }
    else if(e.target.id == 'portfolio-link'){
        /* showPortfolio() */
        getPortfolio()
        nav.querySelector('.active').classList.remove('active')
        e.target.classList.add('active')

    }
})

function getPortfolio(){
    var url = '/public/home';
    var formData = new FormData();
    formData.append('getPortfolio', true);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.json())
    .then(json=>renderPortfolio(json))
} 

/* function showPortfolio(){
    let url = '/public/home';
    let formData = new FormData();
    formData.append('showPortfolio', true);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(text=>main_container.innerHTML = text)
}  */

function renderPortfolio(json){
    document.querySelector('#portfolio-container')!=null?document.querySelector('#portfolio-container').remove():"" 
    main_container.querySelector("#portfolio-table")!=null?document.querySelector('#portfolio-table').remove():"" 

    const watchlist = document.querySelector("#watchlist-container")
    watchlist.style.display="none"
    const template_portfolio = document.querySelector('#template-portfolio').content.cloneNode(true)
    const portfolio_container = document.querySelector('#portfolio-container')
    const tbody_portfolio = template_portfolio.querySelector('#tbody-portfolio')
    const portfolio_table = template_portfolio.querySelector('table')

    const fragment = document.createDocumentFragment()
    const template_watchlist_row = document.querySelector('#template-row-portfolio').content
    json.forEach(element=>{
        const clone = template_watchlist_row.cloneNode(true)
        const a = clone.querySelector('.collection-link')
        const floor_price = clone.querySelector('.floor-price')
        const purchase_price = clone.querySelector('.purchase-price')
        const currency = clone.querySelector('.currency')
        const amount = clone.querySelector('.amount')
        const currency_price = clone.querySelector('.currency-price')
        let current_total_value = clone.querySelector('.current-total-value')
        let total_purchase_value = clone.querySelector('.total-purchase-value')
        let profit = clone.querySelector('.profit')
        const actions = clone.querySelector('.actions')

        actions.dataset.id = element.id_portfolio

        const  url = '/public/searchCollection';
        fetch(url, { 
            method: 'POST', 
            body: JSON.stringify({'symbol': element.symbol}), 
            headers: {
                'Content-Type':'application/json',
            },
        })
        .then(response=>response.json())
        .then(json=>{
            floor_price.textContent = (json.floorPrice/1000000000).toFixed(2)
            a.textContent = json.name
            a.href = `https://magiceden.io/marketplace/${element.symbol}`
            purchase_price.textContent = element.purchase_price
            currency.textContent = `\$${element.currency.toUpperCase()}`
            amount.textContent = element.amount_owned
            //Currency request
            let cmc_url = '/src/getCoinUSD';
            let formData = new FormData();
            formData.append('currency', element.currency.toUpperCase());
            fetch(cmc_url, { 
                method: 'POST', 
                body: formData, 
            })
            .then(resp=>resp.text())
            .then(text=>{
                let c_price = parseFloat(text).toFixed(2)
                currency_price.textContent = c_price
                current_total_value.textContent = (parseFloat(floor_price.textContent)*element.amount_owned*c_price).toFixed(2)
                total_purchase_value.textContent = (element.purchase_price*element.amount_owned*c_price).toFixed(2)
                profit.textContent = (parseFloat(current_total_value.textContent)- parseFloat(total_purchase_value.textContent)).toFixed(2)
                parseFloat(profit.textContent)>0 ? profit.classList = 'text-success': profit.classList = 'text-danger'
            })
        })




        fragment.appendChild(clone)      
    })
    portfolio_table.addEventListener('click',e=>{
        if(e.target.classList.contains('removeItem')){
            removeItem(e.target.parentElement.dataset.id)
            /* showPortfolio() */
            getPortfolio()
        }
    })

    tbody_portfolio.appendChild(fragment)
    portfolio_table.appendChild(tbody_portfolio)
    main_container.appendChild(portfolio_table)
}

function showPortfolioForm(){
    const container = document.querySelector('#form-container')
    container.innerHTML = ""
    const template_portfolio_form = document.querySelector('#template-portfolio-form').content.cloneNode(true)
    portfolio_form != null?portfolio_form.remove():""
    secondary_container.appendChild(template_portfolio_form)

    


} 

function fillPortfolioForm(json){
    const form = document.querySelector('#form-portfolio')

    const name = form.querySelector('#collection-name-input')
    name.value = json.name

    form.querySelector('#input-symbol').value = json.symbol
    form.querySelector('#current-price-input').value = (json.floorPrice/1000000000).toFixed(2)

    //Submit event
    form.addEventListener('submit',e=>{
        e.preventDefault()
        addToPortfolio(form)
        /* showPortfolio() */
        getPortfolio()
        nav.querySelector('.active').classList.remove('active')
        nav.querySelector('#portfolio-link').classList.add('active')
    })

}



function addToPortfolio(form){
    const url = '/public/home'
    const formData = new FormData(form)
    formData.append('addToPortFolio',true)
    formData.append('currency','SOL')
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(text=>console.log(text))
}

function removeItem(id_portfolio){
    const url = '/public/home'
    const formData = new FormData()
    formData.append('removeItem',true)
    formData.append('id_portfolio',id_portfolio)
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(text=>console.log(text))
}
async function getTopCollections(){
    const url = '/public/home'
    const formData = new FormData()
    formData.append('getTopCollections',true)
    const response = await fetch('/public/home', {
                method: 'POST',
                body: formData, 
            });
    const json = await response.json();
    autocomplete(document.getElementById("input-search"), json);
}


/* getTopCollections() */



function autocomplete(inp, arr) {
    console.log(arr[0]);
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array.*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item starts with the same letters as the text field value:*/
          if (arr[i][0].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            b.innerHTML = "<strong>" + arr[i][0].substr(0, val.length) + "</strong>";
            b.innerHTML += arr[i][0].substr(val.length);
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + arr[i][1] + "'>";
            /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                /*insert the value for the autocomplete text field:*/
                inp.value = this.getElementsByTagName("input")[0].value.replaceAll('_',' ');
                /*close the list of autocompleted values,
                (or any other open lists of autocompleted values:*/
                closeAllLists();
            });
            a.appendChild(b);
          }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          /*If the arrow DOWN key is pressed,
          increase the currentFocus variable:*/
          currentFocus++;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 38) { //up
          /*If the arrow UP key is pressed,
          decrease the currentFocus variable:*/
          currentFocus--;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 13) {
          /*If the ENTER key is pressed, prevent the form from being submitted,*/
          e.preventDefault();
          if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
          }
        }
    });
    function addActive(x) {
      /*a function to classify an item as "active":*/
      if (!x) return false;
      /*start by removing the "active" class on all items:*/
      removeActive(x);
      if (currentFocus >= x.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = (x.length - 1);
      /*add class "autocomplete-active":*/
      x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
      /*a function to remove the "active" class from all autocomplete items:*/
      for (var i = 0; i < x.length; i++) {
        x[i].classList.remove("autocomplete-active");
      }
    }
    function closeAllLists(elmnt) {
      /*close all autocomplete lists in the document,
      except the one passed as an argument:*/
      var x = document.getElementsByClassName("autocomplete-items");
      for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
  } 

function renderWatchListForm(json){

    const template = document.querySelector("#template-watchlist-form").content
    const container = document.querySelector('#form-container')
    container.innerHTML = ""
    document.querySelector('#portfolio-form')==null?"":document.querySelector('#portfolio-form').remove()
    const clone = template.cloneNode(true)
    const name = clone.querySelector('#collection-name-input')
    const floor = clone.querySelector('#current-price-input')
    const hidden = clone.querySelector('#input-symbol-watchlist')

    hidden.value = json.symbol
    
    name.value = json.name
    floor.value = parseFloat(json.floorPrice/1000000000).toFixed(2)
    container.appendChild(clone)

    const a_add = document.querySelector('#add-attribute')
    const a_remove = document.querySelector('#remove-attribute')
    const atribute_container = document.querySelector('#attribute-group')
    a_add.addEventListener('click',e=>{
        getAttributeTypes(json.symbol)
    })
    a_remove.addEventListener('click',e=>{
        atribute_container.removeChild(atribute_container.lastElementChild) 
        attribute_counter--
    })
}

function renderAttributeSelect(json){
    const template = document.querySelector("#attribute-template").content
    const clone = template.cloneNode(true)
    const types_select = clone.querySelector('.attribute-types')
    const values_select = clone.querySelector('.attribute-values')
    values_select.innerHTML = ""
    const container = document.querySelector('#attribute-group')

    const fragment = document.createDocumentFragment()

    //Render types
    json.forEach(element=>{
        const option = document.createElement('option')
        option.setAttribute('value',element.id_trait)
        option.textContent = element.trait_type
        fragment.appendChild(option)
    })
    types_select.appendChild(fragment)
    
    //Add event to search values of selected type
    types_select.addEventListener('change',e=>{
        const changed = document.querySelectorAll('.attribute-element')[e.target.dataset.id]
        const attr = changed.querySelector('select').value
        getAttributeValues(attr, e.target.dataset.id)
    })

    //Render values

    container.appendChild(clone)
    types_select.dataset.id = attribute_counter++

}

function getAttributeTypes(symbol){
    var url = '/public/home';
    var formData = new FormData();
    formData.append('getAttributeTypes', true);
    formData.append('symbol', symbol);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.json())
    .then(json=>renderAttributeSelect(json))
}

function getAttributeValues(id_trait, dataset_id){
    var url = '/public/home';
    var formData = new FormData();
    formData.append('getAttributeValues', true);
    formData.append('id_trait', id_trait);
    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.json())
    .then(json=>fillAttributeValues(json, dataset_id))
}

function fillAttributeValues(json, dataset_id){
    const values_select = document.querySelectorAll('.attribute-values')[dataset_id]
    const fragment = document.createDocumentFragment()
    json.forEach(element=>{
        const option = document.createElement('option')
        option.setAttribute('value',element.value_id)
        option.textContent = element.value
        fragment.appendChild(option)
    })
    values_select.appendChild(fragment)
}

function isUrl(text){
    const regex = new RegExp(/(https:\/\/)?magiceden.io\/marketplace\/[a-z]+/gm)
    return regex.test(text)
}



function wlSubmit(){
    const watchlist_form = document.querySelector('#form-watchlist')
    
    const symbol = watchlist_form.querySelector('#input-symbol-watchlist').value
    const compare = watchlist_form.querySelector('#compare').value
    const price = watchlist_form.querySelector('#alert-price-input').value
    const currency = watchlist_form.querySelector('#alert-currency').value
    const duration = watchlist_form.querySelector('#alert-duration-input').value
    const magnitude = watchlist_form.querySelector('#alert-duration-magnitude').value
    
    const formData = new FormData
    formData.append('addAlert', true)
    formData.append('symbol',symbol)
    formData.append('compare', compare)
    formData.append('price',price)
    formData.append('currency',currency)
    formData.append('duration',duration)
    formData.append('magnitude',magnitude)
    
    //Attributes
    const values = watchlist_form.querySelectorAll('.attribute-values')
    let attributes =  []
    values.forEach((element, i) =>{
        attributes.push(values[i].value)
    })
    formData.append('attributes', JSON.stringify(attributes))

    let url = "/public/home"
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(function (response) {
        response.text();
    })
    .then(function (body) {
        getWatchList()
    });
}

function processUpdates(){
    const url = '/public/home';
    let formData = new FormData();
    formData.append('processUpdates', true);    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(text=>console.log(text))
}

function linkTelegram(telegram_id){
    const url = '/public/home';
    let formData = new FormData();
    const submit = document.querySelector('#submit-tid')
    const icon = submit.querySelector('.spinner-border')
    const submit_text = submit.querySelector('#submit-text')
    
    formData.append('linkTelegram', true);    
    formData.append('telegram_id', telegram_id);    
    fetch(url, { 
        method: 'POST', 
        body: formData, 
    })
    .then(response=>response.text())
    .then(text=>{
        if(text == 1){
            //Green button
            isTelegramLinked()
            submit_text.textContent = "Linked!"
            submit.classList.remove('btn-primary')
            submit.classList.add('btn-success')
            submit.classList.remove('loading')
            icon.classList.add('d-none')
            setTimeout(function(){
                modal2.hide()
            },1000)
            submit.classList.remove('bg-success')
            submit.classList.remove('disabled')
        }
        //error
    })
}

function addModalEvents(){
    const modal_form = document.querySelector('#modal-form')
    const submit_tid = document.querySelector('#submit-tid')
    const continue_button = document.querySelector('#continue')
    const close = document.querySelector('#close-modal')
    const back = document.querySelector('#modal-back')
    const icon = submit_tid.querySelector('.spinner-border')
    const text = submit_tid.querySelector('#submit-text')
    const telegram_code = modal_form.querySelector('#telegram-code')
    //Next modal
    continue_button.addEventListener('click',e=>{
        e.preventDefault()
        processUpdates()
        modal1.hide()
        modal2.show()
    })
    //Previous modal
    back.addEventListener('click',e=>{
        e.preventDefault()
        modal2.hide()
        modal1.show()
    })
    //Close
    close.addEventListener('click',e=>{
        e.preventDefault()
        modal1.hide()
    })
    //Submit
    submit_tid.addEventListener('click', e=>{
        e.preventDefault()
        submit_tid.classList.add('loading')
        text.textContent = "Linking"
        submit_tid.classList.add('disabled')
        icon.classList.remove('d-none')
        linkTelegram(telegram_code.value)
    })    
    //Enter
    submit_tid.addEventListener('kepyress', e=>{
        e.preventDefault()
        if(e.key = "Enter"){
            submit_tid.classList.add('loading')
            text.textContent = "Linking"
            submit_tid.classList.add('disabled')
            icon.classList.remove('d-none')
            linkTelegram(telegram_code.value)
        }
    })    

}

function isTelegramLinked(){
    const url = '/public/home';
    let formData = new FormData();
    formData.append("isTelegramLinked",true)
    fetch(url,{
        method: "POST",
        body: formData
    })
    .then(response=>response.text())
    .then(text=>{
        console.log(text);

        if(text == true){
            alert_success.classList.remove("d-none")
            alert_warning.classList.add("d-none")

            linked = true
        }
        else if(text == false){
            alert_warning.classList.remove("d-none")
            addModalEvents()
            linked = false

        }
    })
}

function sendTestAlert(){
    const url = '/public/home';
    let formData = new FormData();
    formData.append("sendTestAlert",true)
    fetch(url,{
        method: "POST",
        body: formData
    })
    .then(response=>response.text())
    .then(text=>console.log(text))
}

function isFormEmpty(form) {
    // get all the inputs within the submitted form
    let inputs = form.getElementsByTagName('input');
    for (let i = 0; i < inputs.length; i++) {
        // only validate the inputs that have the required attribute
        if(inputs[i].hasAttribute("required")){
            if(inputs[i].value == ""){
                // found an empty field that is required
                alert("Please fill all required fields");
                return true;
            }
        }
    }
    return false;
}