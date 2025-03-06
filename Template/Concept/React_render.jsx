// description: React render example
function Car(props) {
    // Return a JSX element
    return <h2>I am a {props.brand}!</h2>;
}

// Function declaration
function Garage() {
    // Return a JSX element
    return <Car brand="Ford" />;
}