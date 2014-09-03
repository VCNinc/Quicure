var items = 1;
var total = 0;
$(function(){
	$("#additem").click(function(){
		items++;
		$("#additem").text("+ Add Item #" + (items + 1));
		$("#itemcount").val(items);
		$("#del").remove();
		$("#items").append('<tr class="item-'+ items +'"><td><input type="text" name="item-' + items + '-name" placeholder="Item #' + items + '" class="form-control input"></td><td><input type="text" name="item-' + items + '-unit-price" placeholder="10.00" class="form-control input"></td><td><input type="text" name="item-' + items + '-quantity" placeholder="4" class="form-control input"></td><td rowspan="2" id="input-' + items + '-total">$0.00<btn class="btn btn-danger" id="del">&times;</btn></td></tr><tr class="item-'+ items +'"><td colspan="3"><input type="text" name="item-' + items + '-description" placeholder="Description (Optional)" class="form-control input"></td></tr>');
		delClick();
	});
});
function delClick() {
		$("#del").click(function(){
			$(".item-" + items).remove();
			items--;
			$("#additem").text("+ Add Item #" + (items + 1));
			$("#itemcount").val(items);
			if(items > 1) {
				$("#input-" + items + "-total").append('<btn class="btn btn-danger" id="del">&times;</btn>');
				delClick();
			}
		});
}