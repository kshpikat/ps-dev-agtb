/*global jCore,

*/
var CommandAnnotationResize = function (receiver) {
    jCore.CommandResize.call(this, receiver);
};

CommandAnnotationResize.prototype.type = 'commandAnnotationResize';

CommandAnnotationResize.prototype.execute = function () {
    jCore.CommandResize.prototype.execute.call(this);
    //this.receiver.graphics.clear();
    this.receiver.paint();
};

CommandAnnotationResize.prototype.undo = function () {
    jCore.CommandResize.prototype.undo.call(this);
    //this.receiver.graphics.clear();
    this.receiver.paint();
};

CommandAnnotationResize.prototype.redo = function () {
    this.execute();
};
