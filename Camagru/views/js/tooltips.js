let showingTooltip;

document.onclick = function(e) {
	let target = e.target;

	let tooltip = target.getAttribute('data-tooltip');
	if (!tooltip)
		return;

	let tooltipElem = document.createElement('div');
	tooltipElem.className = 'tooltip';
	tooltipElem.innerHTML = tooltip;
	document.body.appendChild(tooltipElem);

	let coords = target.getBoundingClientRect();
	let left = coords.left + (target.offsetWidth - tooltipElem.offsetWidth) / 2;
	if (left < 0) left = 0; // do not go beyond the left window border

	let top = coords.top - tooltipElem.offsetHeight - 5;
	if (top < 0) { // do not climb over the top of the window
		top = coords.top + target.offsetHeight + 5;
	}

	tooltipElem.style.left = left + 'px';
	tooltipElem.style.top = top + 'px';

	showingTooltip = tooltipElem;
};

document.onmouseout = function() {

	if (showingTooltip) {
		document.body.removeChild(showingTooltip);
		showingTooltip = null;
	}

};