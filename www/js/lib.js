Array.prototype.prefix = function (prefix) {
    var arr = [];
    for (i = 0; i < this.length; i++) {
        arr[i] = prefix + this[i];
    }
    return arr;
};

Array.prototype.diff = function (a) {
    return this.filter(function (i) {
        return a.indexOf(i) < 0;
    });
};