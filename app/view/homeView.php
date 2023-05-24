<?php

function showHead()
{
    require('../private/config.php');
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NFTprice - Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/script.js" defer></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">

    </head>

    <body>
        <header class="d-flex justify-content-between p-1 align-items-center bg-light border border-dark">
            <a class="text-decoration-none text text-dark"  href="/index"><h1>NFTprice</h1></a>
            <div class="p-3">
                <a class="" href="logout.php">
                    <button class="btn btn-secondary bg-transparent text-dark" type="button">Logout</button>
                    </a>
            </div>
        </header>
        <nav class="container-md mw-50">
            <div class="container-fluid alert alert-warning d-none" id="linked-warning">
                <h5 class="">Telegram not linked</h5>
                <p class="align-middle m-0">
                    We will alert you if any NFT in your watchlist reaches the set price.
                    <a class="alert-link" href="#">Receive price alerts in your Telegram</a>
                </p>
            </div>
            <div class="container-fluid alert alert-success justify-content-between d-none" id="linked-success">
                <p class="align-middle m-0">
                    You will receive your alerts to your Telegram.
                    <a id="send-test-alert" class="alert-link" href="#">Send a test alert</a>
                </p>

                <a id="reconfigure-telegram" href="#" class="text-dark">
                    <div class="">Not working?
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                            <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                        </svg>
                    </div>
                </a>

            </div>
            <ul class="nav nav-tabs">
                <li class="nav-item"><a id="watchlist-link" class="nav-link active" href="#">Watchlist</a></li>
                <li class="nav-item"><a id="portfolio-link" class="nav-link" href="#">Portfolio</a></li>
            </ul>
        </nav>
        <div id="main-container" class="container-md mw-50">

        </div>

    <?php
}


function showSearch()
{
    ?>
        <div id="secondary-container" class="container-lg d-lg-flex mb-5 p-3">
            <div class="container-md mw-50 mb-4 ps-0" id="left-secondary">
                <h2>Find a collection</h2>
                <section id="search">
                    <form id="form-search" class="d-inline-flex pt-3 pe-0 input-group" action="#" method="POST">
                        <input autocomplete="off" id="input-search" class="form-control" type="text" name="symbol" placeholder="Collection name or MagicEden link" required>
                        <input class="btn btn-primary ml-3" type="submit" name="searchCollection" id="searchCollection" value="Search">
                    </form>
                </section>
                <section id="floor" class="container-fluid ps-0 pe-0"></section>
                <section id="collections" class="container-fluid row ms justify-content-center">
                    <!-- Insert here collection template -->
                </section>
            </div>
            <div id="form-container" class="container-md mw-50"></div>
        </div>

    </body>

    </html>
<?php
}
function showTemplates()
{
?>
    <!-- Search template -->
    <template id="template-watchlist-head">
        <div id="watchlist-container">
            <form id="watchlist-form" class="table-responsive" action="home.php" method="GET">
                <table id="watchlist" class="table table-responsive-sm align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Current price</th>
                            <th>Alert price</th>
                            <th class="d-none d-lg-table-cell"></th>
                            <th class="d-none d-lg-table-cell">Currency</th>
                            <th class="th-attributes">Attributes</th>
                            <th class="d-none d-lg-table-cell">State</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbody-watchlist">
                    </tbody>
                </table>
            </form>
        </div>
    </template>
    <template id="template-collection">
        <div class="col-sm-4 col-md-3 col-xl-2 border p-3 m-1">
            <img class="card-img-top" width="150px" src="" alt="">
            <h3 id="collection-name" class="card-title"></h3>
            <p class=""></p>
            <a class="btn btn-primary" href="">See more</a>
        </div>
    </template>
    <template id="template-floor">
        <div class="collection-floor mt-3 border rounded border-secondary p-3">
            <div class="d-flex flex-column">
                <h3 class="name w-100"></h3>
                <div class="d-flex justify-content-between">
                    <div class="w-50 align-self-start">
                        <img src="" alt="" class="img-thumbnail mb-3">
                    </div>
                    <div class="w-50 d-flex flex-column ms-3 me-3">
                        <p class="listed"></p>
                        <p class="volume"></p>
                        <p class="description"></p>
                        <p class="avg-price"></p>
                        <p class="floor fw-bold mt-auto"></p>
                    </div>
                </div>

            </div>
            <div class="d-flex justify-content-between mt-2" id="floor-buttons">
                <button class="addToPortfolio btn btn-outline-primary">Add to porfolio</button>
                <button class="addToWatchlist btn btn-primary">Add to watchlist</button>
            </div>
        </div>
    </template>
    <!-- Watchlist template -->
    <template id="template-row-wl">
        <tr class="">
            <td class=""><a class="collection-link" target="_blank" href="#"></a></td>
            <td class="floor-price"></td>
            <td class="td-alert">
                <input class="alert-price noborder p-0 " type="text" name="alert-price" disabled>
            </td>
            <td class="p-0 d-none d-lg-table-cell">
                <div class="icon-container">
                    <i class="edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z" />
                        </svg>
                    </i>
                    <i class="check">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />
                        </svg>
                    </i>
                </div>
            </td>
            <td class="currency d-none d-lg-table-cell">$SOL</td>
            <td class="attributes"></td>
            <td class="active d-none d-lg-table-cell"></td>
            <td class="actions d-flex justify-content-end">
                <button type="submit" name="activate" value="" class="btn btn-success m-1">On</button>
                <button type="submit" name="turnOff" value="" class="btn btn-warning m-1">Off</button>
                <button type="submit" name="remove" value="" class="btn btn-danger m-1">X</button>
            </td>
        </tr>
    </template>
    <!-- Porfolio template -->
    <template id="template-row-portfolio">
        <tr class="">
            <td class="name"><a class="collection-link" target="_blank" href="#"></a></td>
            <td class="floor-price"></td>
            <td class="purchase-price"></td>
            <td class="currency">$SOL</td>
            <td class="amount"></td>
            <td class="currency-price"></td>
            <td class="current-total-value"></td>
            <td class="total-purchase-value"></td>
            <td class="profit"></td>
            <td class="actions d-flex justify-content-end">
                <button type="submit" name="remove" value="" class="btn btn-danger m-1 removeItem">Remove</button>
                <i class="check">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z" />
                    </svg>
                </i>

            </td>
        </tr>
    </template>
    <template id="template-portfolio-form">
        <div id="portfolio-form" class="container-xxl">

            <h2 class="mb-3">Add to portfolio</h2>
            <form class="" id="form-portfolio">
                <div id="inline1" class="form-row">
                    <div class="">
                        <label class="sr-only" for="collection-name-input">Name</label>
                        <input disabled type="text" class="form-control mb-2 mr-sm-2" id="collection-name-input" name="name" placeholder="Search collection name">
                    </div>
                    <div class="">
                        <label for="current-price-input">Current floor price</label>
                        <div class="input-group mb-2">
                            <input disabled type="text" class="form-control mb-2 mr-sm-2" id="current-price-input" name="current-price">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$SOL
                                    <!-- CURRENCY -->
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div id="inline2" class="form-row">
                    <label class="sr-only" for="purchase-price-input">Purchase price</label>
                    <div class="input-group mb-2 mr-sm-2">
                        <input type="text" class="form-control mb-2 mr-sm-2" id="purchase-price-input" placeholder="How much did you pay?" name="purchase-price">
                        <div class="input-group-prepend">
                            <div class="input-group-text">$SOL
                                <!-- CURRENCY -->
                            </div>
                        </div>
                    </div>

                    <label class="sr-only" for="amount-owned-input">Amount owned</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="amount-owned-input" placeholder="Number of NFT's you bought at the same time" name="amount-owned">

                    <label class="sr-only" for="currency-price-input">Currency price ($)</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="currency-price-input" name="currency-price" placeholder="How much did you pay for the CURRENCY you spent (average)?">
                </div>
                <input type="hidden" name="symbol" id="input-symbol">
                <button type="submit" id="submit-portfolio" class="btn btn-primary mb-2">Submit</button>
                <button type="button" id="cancel-form" class="btn btn-danger mb-2">Cancel</button>
            </form>
        </div>
    </template>
    <template id="template-portfolio">
        <div id="portfolio-container" class="container-xl">
            <form id="portfolio-form" action="home.php" method="GET">
                <table id="portfolio-table" class="table table-responsive-sm align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Current floor</th>
                            <th>Purchase price</th>
                            <th>Currency</th>
                            <th>Amount owned</th>
                            <th>Currency price ($)</th>
                            <th>Current value ($)</th>
                            <th>Purchase value ($)</th>
                            <th>Profit ($)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbody-portfolio">
                    </tbody>
                </table>
            </form>
        </div>
    </template>
    <template id="template-watchlist-form">
        <div id="watchlist-form-container" class="">

            <h2 class="mb-3">Add to watchlist</h2>
            <form class="" id="form-watchlist" method="POST">
                <div id="inline2" class="form-row">
                    <div class="">
                        <label class="sr-only" for="collection-name-input">Name</label>
                        <input disabled type="text" class="form-control mb-2 mr-sm-2" id="collection-name-input" name="name" placeholder="Search collection name">
                    </div>
                    <div class="">
                        <label for="current-price-input">Current floor price</label>
                        <div class="input-group mb-2">
                            <input disabled type="text" class="form-control mb-2 mr-sm-2" id="current-price-input" name="current-price">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$SOL
                                    <!-- CURRENCY -->
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div id="inline2" class="form-row">
                    <label class="sr-only" for="alert-price-input">Alert price</label>
                    <div class="input-group mb-2 mr-sm-2">
                        <select name="compare" id="compare" class="custom-select mb-2 mr-sm-2 rounded w-25">
                            <option value="lower">Lower than</option>
                            <option value="greater">Greater than</option>
                        </select>
                        <input type="number" required class="form-control mb-2 mr-sm-2" id="alert-price-input" placeholder="Your desired price" name="alert-price">
                        <select class="custom-select mb-2 mr-sm-2 rounded-right rounded w-25" name="currency" id="alert-currency">
                            <option value="sol">$SOL</option>
                            <option value="usd">$USD</option>
                        </select>

                    </div>
                </div>
                <label class="sr-only" for="inputGroupSelect01">Alert duration</label>
                <div class="input-group mb-3">
                    <input type="number" required min="1" max="30" class="form-control mb-2 mr-sm-2" id="alert-duration-input" placeholder="Time until inactive (max 30 days)" name="alert-duration-input">
                    <select class="custom-select mb-2 mr-sm-2 rounded-right rounded w-25" name="magnitude" id="alert-duration-magnitude" required>
                        <option value="hours">Hours</option>
                        <option value="days">Days</option>
                    </select>
                </div>
                <div id="attribute-group-container" class="mb-2 mr-sm-2">
                    <h5 id="att-title" class="mb-4">Attributes</h5>
                    <div class="attribute-group"></div>
                    <div class="d-flex justify-content-between">
                        <a id="remove-attribute" href="#att-title" class="text-decoration-none mt-2 text-danger">
                            <i class="icon-container">
                                <svg class="mb-1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z" />
                                </svg>
                            </i>
                            Remove attribute
                        </a>
                        <a id="add-attribute" href="#att-title" class="text-decoration-none mt-2">
                            <i class="icon-container ">
                                <svg class="mb-1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                </svg>
                            </i>
                            Add a new attribute
                        </a>
                    </div>
                </div>
                <input type="hidden" name="symbol" id="input-symbol-watchlist">
                <div class="button-group mt-4 d-flex justify-content-between">
                    <button type="button" id="cancel-form" class="btn btn-danger m-2">Cancel</button>
                    <button type="submit" id="submit-watchlist" class="btn btn-primary mb-2 mt-2" data-bs-target="#modal">Submit</button>
                </div>
            </form>
        </div>
    </template>
    <template id="attribute-template">
        <div class="attribute-element border-bottom border-secondary pb-3 mb-3">
            <div class="input-group d-flex mb-2">
                <div class="input-group-prepend w-25">
                    <label class="input-group-text" for="attribute-types">Type</label>
                </div>
                <select name="attribute-types" id="" class="attribute-types custom-select flex-grow-1">
                    <option value="-1">None</option>
                </select>
            </div>
            <div class="input-group d-flex">
                <div class="input-group-prepend w-25">
                    <label class="input-group-text" for="attribute-values">Value</label>
                </div>
                <select name="attribute-values" id="" class="attribute-values custom-select flex-grow-1">
                    <option value="-1">None</option>
                </select>
            </div>
        </div>
    </template>

    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Receive your alerts on Telegram</h5>
                    <button type="button" id="close-modal" class="btn-close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center mb-2">
                        <a href="https://t.me/floorPriced_bot" target="_blank">
                            <img class="border rounded p-2" src="../img/qr_bot.png" alt="QR code to https://t.me/floorPriced_bot">
                        </a>
                    </div>
                    <p>Scan, click or follow the steps:</p>
                    <ol>
                        <li>Launch Telegram</li>
                        <li class="bold">Search @floorPriced_bot</li>
                        <li>Press Start</li>
                    </ol>

                    <div class="modal-footer">
                        <button type="button" id="continue" class="btn btn-primary" data-bs-target="#modal2">Continue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal2" aria-hidden="true" aria-labelledby="modal2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel2">Modal 2</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="modal-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code" class="col-form-label">Enter the code the bot just sent you:</label>
                            <input type="text" class="form-control" id="telegram-code" name="telegram-code">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="modal-back" class="btn btn-secondary" data-bs-target="#modal1">Back</button>
                        <button id="submit-tid" class="btn btn-primary" type="submit">
                            <span id="submit-text" class="mr-2">Link telegram</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    <!-- <a class="btn btn-primary" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Open first modal</a> -->

    

<?php

}
