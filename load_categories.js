function loadCategories() {
	fetch('get_categories.php')
		.then(response => response.json())
		.then(data => {
			var categorySelect = document.getElementById('categorySelect');
			categorySelect.innerHTML = '';

			data.forEach(category => {
				var option = document.createElement('option');
				option.value = category.category_id;
				option.textContent = category.name;
				categorySelect.appendChild(option);
			});
		})
		.catch(error => {
			console.error('Ошибка загрузки категорий:', error);
		});
}

loadCategories();