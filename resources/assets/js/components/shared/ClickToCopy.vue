<template>
    <p @click="copy">{{ text }}</p>
</template>
<script>
    export default {
        props: ['text'],
        
        methods: {
            copy(e) {
                const text = e.target.innerText;
                
                const el = document.createElement('textarea');  // Create a <textarea> element
                el.value = text;                                // Set its value to the string that you want copied
                el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
                el.style.position = 'absolute';                 
                el.style.left = '-9999px';                      // Move outside the screen to make it invisible
                document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
                const selected =            
                    document.getSelection().rangeCount > 0      // Check if there is any content selected previously
                    ? document.getSelection().getRangeAt(0)     // Store selection if found
                    : false;                                    // Mark as false to know no selection existed before
                el.select();                                    // Select the <textarea> content
                document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
                document.body.removeChild(el);                  // Remove the <textarea> element
                if (selected) {                                 // If a selection existed before copying
                    document.getSelection().removeAllRanges();  // Unselect everything on the HTML document
                    document.getSelection().addRange(selected); // Restore the original selection
                }
                
                e.target.classList.toggle("copied");
                setTimeout(() => {
                    e.target.classList.toggle("copied");
                }, 1000);
            }
        }
    };
</script>

<style scoped>
    p {
        cursor: pointer;
        padding: 7px;
        background: #f7f7f7;
        border-radius: 10px;
        border: .5px solid #e8e8e8;
        position: relative;
        z-index: 5;
    }
    
    p::after {
        content: "Copied";
        color: white;
        background-color: black;
        display: inline-block;
        padding: 2px 20px;
        position: absolute;
        left: 50%;
        top: 0;
        border-radius: 1000px;
        transform: translateX(-50%);
        z-index: 3;
        opacity: 0;
        transition: .5s all ease-in-out;
    }
    
    p.copied::after {
        opacity: 1;
        transform: translateX(-50%) translateY(200%);
    }
    
    p:hover {
        background: #e8e8e8;
    }
</style>