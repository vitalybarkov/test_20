var appLocation = 'http://localhost/tests/test_20'
var apples = {};
document.getElementById("growApples").addEventListener("click", growApples);

function renderApples (apples) {
    // clear the table
    document.getElementById('Apples').innerHTML = '';

    // add apple's cards
    for (let $i = 1; $i < apples.payload.length + 1; $i++) {
        let apple = document.createElement("DIV");
        apple.classList.add("a");
        apple.id = "apple-" + $i;
        document.getElementById('Apples').appendChild(apple);

        let pic = document.createElement("DIV");
        pic.classList.add("pic");
        apple.appendChild(pic);

        let picImg = document.createElement("IMG");
        picImg.src = './v/i/ag.png';
        picImg.classList.add(apples.payload[$i - 1].color);
        picImg.style.marginTop = "0%";
        pic.appendChild(picImg);
        
        let btn = document.createElement("BUTTON");
        btn.classList.add("drop");
        btn.innerHTML = 'DROP';
        btn.addEventListener("click", function() {
            dropApple($i);
        });
        apple.appendChild(btn);

        let eat = document.createElement("DIV");
        eat.classList.add("eat");
        apple.appendChild(eat);

        let percent = document.createElement("LABEL");
        percent.innerHTML = '%';
        eat.appendChild(percent);

        let inpPercent = document.createElement("INPUT");
        inpPercent.value = '0';
        eat.appendChild(inpPercent);

        let eatBtn = document.createElement("BUTTON");
        eatBtn.innerHTML = 'EAT';
        eatBtn.addEventListener("click", function() {
            eatApple($i, inpPercent.value);
        });
        eat.appendChild(eatBtn);
    }
}

function getAjax (url, success) {
    let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    xhr.open('GET', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
    return xhr;
}

// example request
// getAjax(appLocation + '/GrowApples', function(data){ console.log(data); });

function postAjax (url, data, success) {
    let params = typeof data == 'string' ? data : Object.keys(data).map(
            function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
        ).join('&');

        let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
}

// example request
// postAjax(appLocation + '/EditApple', {id: 2, drop: 1, eat: 19}, function(data){ console.log(data); });

function growApples () {
    getAjax(appLocation + '/GrowApples', function(data){
        apples = JSON.parse(data);
        renderApples(apples);
        console.log(apples);
    });
}

function dropApple (i) {
    // drop the apple
    postAjax(appLocation + '/EditApple', {id: i, drop: 1}, function(data){
        console.log(data);
    });

    // change UI buttons for the apple card
    let btnDrop = document.getElementById('apple-' + i);
    btnDrop.getElementsByTagName('button')[0].className = 'dropOff';
    let inpEat = btnDrop.getElementsByTagName('div')[1].className = 'eatOn';
}

function eatApple (i, percentage) {
    // eat the apple
    var eatedPercentage = null;
    postAjax(appLocation + '/EditApple', {id: i, eat: percentage}, function(data) {
        console.log(data);
        parseData = JSON.parse(data);
        if (parseData.payload) {
            changeEatedPercentage(parseData); 
        }

    });

    function changeEatedPercentage (data) {
        if (!data.payload.deleted) {
            let btnDrop = document.getElementById('apple-' + i);
            btnDrop.getElementsByClassName('pic')[0].getElementsByTagName('img')[0].style.marginTop = data.payload.eatedPercentage + '%';
        } else {
            deleteApple(i);
        }
    }   
}

function deleteApple (i) {
    // change UI buttons for the apple card
    let btnDrop = document.getElementById('apple-' + i);
    btnDrop.className = 'a off';
}