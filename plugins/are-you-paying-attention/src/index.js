wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
    title: "Are You Paying Attention?",
    icon: "smiley",
    category: "common",
    attributes:{
        skyColor: {type : "string"},
        grassColor: {type : "string"}
    },
    edit: function (props) {
        function updateSkyColor(event){
            props.setAttributes({skyColor: event.target.value})
        }
        function updateGrassColor(event){
            props.setAttributes({grassColor: event.target.value})
        }
        return (
          <div>
              <input type="text" placeholder="sky color" onChange={updateSkyColor} />
              <input type="text" placeholder="grass color" onChange={updateGrassColor} />
          </div>
      )
    },
    save: function (props) {
        <p>Today the sky is {props.attributes.skyColor} and the grass is {props.attributes.grassColor}.</p>
    }
  })