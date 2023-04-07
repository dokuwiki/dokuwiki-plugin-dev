const plugin = document.getElementById('plugin_info');
const component = document.getElementById('plugin_components');
const output = document.getElementById('output');
updatePluginNameStatus();

/**
 * disable the plugin name field when a component has been added
 */
function updatePluginNameStatus() {
    plugin.querySelector('input').readOnly = output.children.length > 0;
    document.querySelector('button[type=submit]').disabled = output.children.length === 0;
}

/**
 * Create the HTML element for a component
 *
 * @param {string} plugin The plugin base name
 * @param {string} type The component type
 * @param {string} name The component name
 * @returns {HTMLLIElement|null} null if the component already exists
 */
function createComponentElement(plugin, type, name) {
    let id = `${type}_plugin_${plugin}`;
    if (name !== '') {
        id += `_${name}`;
    }

    if (document.getElementById(`component-${id}`)) {
        return null;
    }

    const li = document.createElement('li');
    li.id = `component-${id}`;
    li.dataset.type = type;
    li.dataset.name = name;

    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'components[]';
    hidden.value = id;
    li.append(hidden);

    const remove = document.createElement('button');
    remove.type = 'button';
    remove.innerText = 'X';
    remove.title = 'Remove this component';
    remove.addEventListener('click', function (event) {
        li.remove();
        updatePluginNameStatus();
    });
    li.append(remove);

    const label = document.createElement('span');
    label.innerText = id;
    li.append(label);


    // add auto completion for events
    if (type === 'action') {
        const events = document.createElement('input');
        events.type = 'text';
        events.name = `options[${id}]`;
        events.placeholder = 'EVENTS_TO_HANDLE, ...';
        li.append(events);
        new Awesomplete(events, {
            list: ACTION_EVENTS,
            filter: function (text, input) {
                return Awesomplete.FILTER_CONTAINS(text, input.match(/[^,]*$/)[0]);
            },

            item: function (text, input) {
                return Awesomplete.ITEM(text, input.match(/[^,]*$/)[0]);
            },

            replace: function (text) {
                var before = this.input.value.match(/^.+,\s*|/)[0];
                this.input.value = before + text + ", ";
            }
        });
    }


    return li;
}


/**
 * Add a component to the output list
 */
component.querySelector('button').addEventListener('click', function (event) {
    const plugin_base = plugin.querySelector('input'); // first input field is plugin base name
    const component_type = component.querySelector('select');
    const component_name = component.querySelector('input');

    if (!plugin_base.validity.valid) {
        plugin_base.reportValidity();
        plugin_base.focus();
        return;
    }

    if (!component_name.validity.valid) {
        component_name.reportValidity();
        component_name.focus();
        return;
    }

    const li = createComponentElement(plugin_base.value, component_type.value, component_name.value);
    if (!li) return;

    output.appendChild(li);
    updatePluginNameStatus();
});
