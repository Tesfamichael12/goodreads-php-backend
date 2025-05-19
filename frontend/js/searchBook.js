const searchInputs = document.querySelectorAll('.search-input');
const searchResultGrid = document.querySelector('#search-results');
const searchResultSection = document.querySelector('.search-result');

async function searchBook(query) {
  console.log('Searching for:', query);
  if (!query) return;

  // Show the section when input is detected
  searchResultSection.style.display = 'block';

  try {
    console.log('Fetching books...');
    const res = await fetch('http://localhost/goodreads-php-backend/backend/books/search.php?search=' + encodeURIComponent(query));
    console.log('Response:', res);
    const books = await res.json();
    console.log('Books found:', books);
    searchResultGrid.innerHTML = '';

    if (!Array.isArray(books) || books.length === 0) {
      console.log('No books found');
      searchResultGrid.innerHTML = '<div>No books found.</div>';
      return;
    }

    books.forEach(book => {
      searchResultGrid.innerHTML += `
        <div class="book-card">
          <a href="./Pages/BookDisplay.html?id=${book.id}">
            <img src="../assets/image/books-image/book1.png" alt="${book.title}" class="book-cover" />
          </a>
          <h3 class="book-title">${book.title}</h3>
          <p class="book-author">${book.author}</p>
        </div>
      `;
    });
  } catch (e) {
    searchResultGrid.innerHTML = '<div>Error loading books.</div>';
  }
}

function debounce(fn, delay) {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
}

searchInputs.forEach(input => {
  input.addEventListener('input', debounce(function (e) {
    const query = e.target.value.trim();
    if (query.length > 0) {
      searchBook(query);
    } else {
      searchResultGrid.innerHTML = '';
      searchResultSection.style.display = 'none'; // Hide if input is cleared
    }
  }, 300));
});
