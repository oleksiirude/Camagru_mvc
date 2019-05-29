function dragndrop(e) {

    let parent = document.getElementsByClassName('workplace')[0];
    let elem = e.target;

    let limits = {
        top: parent.offsetTop,
        right: parent.offsetWidth + parent.offsetLeft - elem.offsetWidth + 10,
        bottom: parent.offsetHeight + parent.offsetTop - elem.offsetHeight + 10,
        left: parent.offsetLeft
    };

    let object = e.target;
    object.style.zIndex = '1000';

    parent.appendChild(object);
    moveAt(e);

    function moveAt(e) {
        let newLocation = {
            x: limits.left,
            y: limits.top
        };
        if (e.pageX > limits.right) {
            newLocation.x = limits.right - 10;
        } else if (e.pageX > limits.left) {
            newLocation.x = e.pageX - 10;
        }
        if (e.pageY > limits.bottom) {
                newLocation.y = limits.bottom - 10;
        } else if (e.pageY > limits.top) {
            newLocation.y = e.pageY - 10;
        }
        relocate(newLocation);
    }

    function relocate(newLocation) {
        elem.style.left = newLocation.x + 'px';
        elem.style.top = newLocation.y + 'px';
    }

    document.onmousemove = (e) => { moveAt(e); };

    object.onmouseup = () => {
        document.onmousemove = null;
        object.onmouseup = null;
    };

    object.ondragstart = () => { return false; };

    object.ondblclick = () => {
        let size, style;

        size = object.offsetWidth + 10;
        style = object.style;
        style.width = size + 'px';
    };

    object.oncontextmenu = () => {
        let size, style;

        if (object.offsetWidth > 40) {
            size = object.offsetWidth - 10;
            style = object.style;
            style.width = size + 'px';
        }
        return false;
    };
}

function create_clone(e) {
    let test = document.getElementsByClassName('workplace').length;
    if (test < 2) {
        let snap = document.getElementById('snap');
        snap.disabled = false;
    }
    let new_mask = document.createElement('img');
    new_mask.setAttribute('src', e.target.src);
    new_mask.setAttribute('class', 'mask');
    new_mask.setAttribute('style', 'position: absolute');
    new_mask.addEventListener('mousedown', dragndrop);
    let parent = document.getElementsByClassName('workplace')[0];
    parent.insertBefore(new_mask, parent.firstChild);
}

let snap = document.getElementById('snap');
if (snap)
    snap.disabled = true;
let masks = document.getElementsByClassName('mask');
if (masks)
    for (let i = 0; i < masks.length; i++)
        masks[i].addEventListener('click', create_clone);