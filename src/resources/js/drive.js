$(() =>
{
	$(".ui.dropdown").dropdown({
		"action": "select",
		"onChange": (_, __, SelectedItems) =>
		{
			if (!SelectedItems || SelectedItems.length < 1) return;

			let SelectedItem = SelectedItems[0];
			if (!SelectedItem) return;

			let SelectedText = SelectedItem.textContent;
			if (!SelectedText) return;

			SelectedText = SelectedText.trim();
			if (SelectedText.length < 1) return;

			switch (SelectedText)
			{
				case "Upload File":
					$(".ui.modal").modal("show");
					break;
			}
		}
	});

	$("#file_upload_modal .approve.button").on("click", () =>
	{
		console.log("yes!");
	});

	$("#file_upload_modal .deny.button").on("click", () =>
	{
		console.log("no!");
	});
});
