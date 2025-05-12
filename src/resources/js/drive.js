function SendFileDelete(ID)
{
	let Data = new FormData();
	Data.append("token", $("meta[name=\"csrf\"]").attr("content"));
	Data.append("id", ID);

	$.ajax({
		"url": "/drive/delete",
		"type": "POST",
		"data": Data,
		"processData": false,
		"contentType": false,
		"success": () => { window.location.reload(); },
		"error": () => { window.location.reload(); } // TODO: Show error
	});
}

function SendFileDownload(ID)
{
	// let Data = new FormData();

	// $.ajax({
	// 	"url": `/drive/download/${ID}`,
	// 	"type": "GET",
	// 	"data": Data,
	// 	"processData": false,
	// 	"contentType": false,
	// 	"success": () => { },
	// 	"error": () => { }
	// });

	window.location.href = `/drive/download/${ID}`;
}

$(() =>
{
	let CurrentPath = window.location.pathname.replace("/drive/main", "").replace(/^\/+/, "");

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
				{
					$("#file_upload_modal").modal("show");
					break;
				}

				case "New Folder":
				{
					$("#folder_creation_modal").modal("show");
					break;
				}
			}
		}
	});

	$("#folder_creation_modal .approve.button").on("click", () =>
	{
		let Form = $("#folder_creation_modal form");
		if (!Form || Form.length < 1) return;

		let Data = new FormData(Form[0]);

		let FolderName = Data.get("name");
        if (!FolderName || FolderName.trim().length < 1) return;

		Data.append("token", $("meta[name=\"csrf\"]").attr("content"));

		let FolderPath = CurrentPath.length > 0 ? CurrentPath + "/" + FolderName : FolderName;
        FolderPath = FolderPath.replace(/^\/+/, "");

		Data.append("path", FolderPath);

		$.ajax({
			"url": "/drive/folder/create",
			"type": "POST",
			"data": Data,
			"processData": false,
			"contentType": false,
			"success": () => { window.location.reload(); },
			"error": () => { window.location.reload(); } // TODO: Show error
		});
	});

	$("#file_upload_modal .approve.button").on("click", () =>
	{
		let Form = $("#file_upload_modal form");
		if (!Form || Form.length < 1) return;

		let Data = new FormData(Form[0]);
		Data.append("path", CurrentPath);
		Data.append("token", $("meta[name=\"csrf\"]").attr("content"));

		$.ajax({
			"url": "/drive/upload",
			"type": "POST",
			"data": Data,
			"processData": false,
			"contentType": false,
			"success": () => { window.location.reload(); },
			"error": () => { window.location.reload(); } // TODO: Show error
		});
	});

	$("#file_rename_modal .approve.button").on("click", () =>
	{
		let Form = $("#file_rename_modal form");
		if (!Form || Form.length < 1) return;

		let Data = new FormData(Form[0]);
		Data.append("token", $("meta[name=\"csrf\"]").attr("content"));
		Data.append("id", window.RenamingFile); // TODO: This is gross

		let FileName = Data.get("name");
        if (!FileName || FileName.trim().length < 1) return;

		$.ajax({
			"url": "/drive/rename",
			"type": "POST",
			"data": Data,
			"processData": false,
			"contentType": false,
			"success": () => { window.location.reload(); },
			"error": () => { window.location.reload(); } // TODO: Show error
		});
	});

	$("#file_share_modal .approve.button").on("click", () =>
	{
		let Form = $("#file_share_modal form");
		if (!Form || Form.length < 1) return;

		let Data = new FormData(Form[0]);
		Data.append("token", $("meta[name=\"csrf\"]").attr("content"));
		Data.append("id", window.RenamingFile); // TODO: This is gross

		let Email = Data.get("email");
        if (!Email || Email.trim().length < 1) return;

		$.ajax({
			"url": "/drive/share",
			"type": "POST",
			"data": Data,
			"processData": false,
			"contentType": false,
			"success": () => { },
			"error": () => { } // TODO: Show error
		});
	});
});
