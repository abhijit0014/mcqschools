

// -------------------------- category choise --------------------------- 

function setUserChoice(category_id) {
    arr = Cookies.get('user_category_choice_list');
    if (!arr) arr = {};
    else arr = JSON.parse(arr);

    if (category_id && category_id.trim() != '') {
        key = '' + category_id;
        if (!(key in arr)) {
            arr[key] = 0;
        }
        for (property in arr) {
            if (property == key) arr[property] = arr[property] + 1;
            else if (arr[property] > 0) arr[property] = arr[property] - 1;

            if (arr[property] == 0)
                if (Object.keys(arr).length > 4)
                    delete arr[property];
        }
        Cookies.set('user_category_choice_list', JSON.stringify(arr), { expires: 700 });
    }
}

function getUserChoiceList() {
    arr = Cookies.get('user_category_choice_list');
    if (!arr) arr = {};
    else arr = JSON.parse(arr);

    result = [];
    sortProperties(arr).forEach(element => {
        result.push(parseInt(element[0]));
    });

    return result;
}

function sortProperties(obj) {
    // convert object into array
    var sortable = [];
    for (var key in obj)
        if (obj.hasOwnProperty(key))
            sortable.push([key, obj[key]]); // each item is an array in format [key, value]

    // sort items by value
    sortable.sort(function (a, b) { return b[1] - a[1]; });
    return sortable; // array in format [ [ key1, val1 ], [ key2, val2 ], ... ]
}
// getUserChoiceList(category_id);
