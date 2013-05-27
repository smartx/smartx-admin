function MyControl(controlName) {
        var controlDiv = document.createElement("div");

        // Set CSS styles for the DIV containing the control
        // Setting padding to 5 px will offset the control
        // from the edge of the map.
        controlDiv.style.padding = '5px';
        controlDiv.id=controlName;

        // Set CSS for the control border.
        var controlUI = document.createElement('div');
        controlUI.style.backgroundColor = 'white';
        controlUI.style.borderStyle = 'solid';
        controlUI.style.borderWidth = '2px';
        controlUI.style.cursor = 'pointer';
        controlUI.style.textAlign = 'center';
        controlUI.title = 'Click to set the map to Home';
        controlDiv.appendChild(controlUI);

        // Set CSS for the control interior.
        var controlText = document.createElement('div');
        controlText.style.fontFamily = 'Arial,sans-serif';
        controlText.style.fontSize = '12px';
        controlText.style.paddingLeft = '4px';
        controlText.style.paddingRight = '4px';
        controlText.innerHTML = '<strong>'+controlName+'</strong>';
        controlUI.appendChild(controlText);

        controlDiv.index = 1;
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);
        return controlDiv;
}

function getPrettyDistance(distance) {
  if (distance > 1) {
    return sprintf("%.2f km", distance);
  } else {
    return sprintf("%.1f m", distance * 1000);
  }
}