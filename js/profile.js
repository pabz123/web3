function handleEditProfile() {
  const currentName = document.querySelector('#profile-name').textContent;
  const newName = prompt('Enter your new name:', currentName);

  if (newName && newName.trim() !== '') {
    document.querySelector('#profile-name').textContent = newName.trim();

    // Update localStorage
    const userData = JSON.parse(localStorage.getItem('userProfile')) || {};
    userData.name = newName.trim();
    localStorage.setItem('userProfile', JSON.stringify(userData));

    showNotification('Profile updated successfully!');
  }
}
