window.undoManagerLogics = new function()
{
	this.initHandler = function()
	{

	}
	this.registerAction = function(component, actionType, beforeData, afterData)
	{
		var actionsItem = {};
		actionsItem['component'] = component;
		actionsItem['actionType'] = actionType;
		actionsItem['beforeData'] = beforeData;
		actionsItem['afterData'] = afterData;

		if (this.currentActionNumber +1 < this.actionsList.length)
		{
			this.actionsList.splice(this.currentActionNumber+1, this.actionsList.length - this.currentActionNumber);
		}
		this.actionsList.push(actionsItem);
		this.currentActionNumber = this.actionsList.length-1;

		controller.fireEvent('undoStateChanged');
	}
	this.getUndoCount = function()
	{
		var count = 0;
		if (this.currentActionNumber >= 0)
		{
			count = this.currentActionNumber + 1;
		}
		return count;
	}
	this.getRedoCount = function()
	{
		var count = this.actionsList.length - this.currentActionNumber - 1;
		return count;
	}
	this.performUndo = function()
	{
		if (this.currentActionNumber >= 0)
		{
			var action = this.actionsList[this.currentActionNumber];
			action.component.performUndo(action.actionType, action.beforeData, action.afterData);

			this.currentActionNumber--;
			controller.fireEvent('undoStateChanged');
		}
	}
	this.performRedo = function()
	{
		if ((this.currentActionNumber >= -1) && (this.currentActionNumber < this.actionsList.length - 1))
		{
			this.currentActionNumber++;

			var action = this.actionsList[this.currentActionNumber];
			action.component.performRedo(action.actionType, action.beforeData, action.afterData);

			controller.fireEvent('undoStateChanged');
		}
	}
	var self = this;
	this.currentActionNumber = -1;
	this.actionsList = new Array();

	controller.addListener('initLogics', this.initHandler);
}