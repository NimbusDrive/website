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

			console.log(SelectedText);

			switch (SelectedText)
			{

			}
		}
	});
});
