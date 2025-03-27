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
					$("#file_upload_modal").modal("show");
					break;
			}
		}
	});

	$("#file_upload_modal .approve.button").on("click", () =>
	{
		let UploadForm = $("#file_upload_modal form");
		if (!UploadForm || UploadForm.length < 1) return;

		let Data = new FormData(UploadForm[0]);

		$.ajax({
			"url": "/drive/upload",
			"type": "POST",
			"data": Data,
			"processData": false,
			"contentType": false,
			"success": () => { }, // TODO: ???
			"error": () => { } // TODO: ???
		});
	});
});
