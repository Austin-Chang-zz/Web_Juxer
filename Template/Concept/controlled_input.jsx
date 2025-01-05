// function declaration
function ControlledInput() {
    // State Initialization
    const [value, setValue] = React.useState('');
    
    // Return a controlled input
    return (
        // Input element
        <input
            // value is controlled by the state
            value={value}
            // onChange event updates the state
            onChange={
                // Event handler
                (e) => setValue(
                    // Update the state with the new value
                    e.target.value)} />
  );
}