import React from 'react';
import ReactDOM from 'react-dom/client';
// Function declaration
function Car(props) {
    // Return a JSX element
    return
    <h2>I am a
        // Interpolate the brand prop
        {props.brand}!
    </h2>;
}
// Element
const myElement = <Car brand="Ford" />;
// Render the element
const root = ReactDOM.createRoot(
    // Root element
    document.getElementById('root'));
// Render the element
root.render(myElement);