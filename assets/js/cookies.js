/**
 * Forge Frame Studios — Cookie utilities
 * recent_products: JSON array of slugs, most recent first, max 5
 * visit_counts: JSON object { slug: count, ... }
 * path=/; max-age=31536000 (1 year)
 */

var COOKIE_RECENT = 'recent_products';
var COOKIE_VISITS = 'visit_counts';
var MAX_RECENT = 5;
var COOKIE_OPTS = 'path=/; max-age=31536000';
var PLACEHOLDER_IMG = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBEQACEQADAPwA/9k=';

/**
 * Get a cookie value by name
 * @param {string} name - Cookie name
 * @returns {string|null} Cookie value or null
 */
function getCookie(name) {
  var match = document.cookie.match(new RegExp('(^| )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]+)'));
  if (!match) return null;
  try {
    return decodeURIComponent(match[2].trim());
  } catch (e) {
    return match[2].trim();
  }
}

/**
 * Set a cookie
 * @param {string} name - Cookie name
 * @param {string} value - Cookie value
 * @param {string} opts - Optional string (e.g. path=/; max-age=31536000)
 */
function setCookie(name, value, opts) {
  document.cookie = name + '=' + encodeURIComponent(value) + '; ' + (opts || COOKIE_OPTS);
}

/**
 * Check if cookies are enabled (write and read back)
 * @returns {boolean}
 */
function cookiesEnabled() {
  var test = 'cookies_test_' + Date.now();
  setCookie(test, '1');
  var ok = getCookie(test) === '1';
  document.cookie = test + '=; path=/; max-age=0';
  return ok;
}

/**
 * Update recent_products: add slug at front, keep max 5, no duplicates
 * @param {string} slug - Current product slug
 * @param {Array} meta - Optional array of { slug, title, image, alt } for all products (for display)
 */
function updateRecentProducts(slug, meta) {
  if (!slug) return;
  var raw = getCookie(COOKIE_RECENT);
  var arr = [];
  try {
    if (raw) arr = JSON.parse(raw);
  } catch (e) {
    arr = [];
  }
  if (!Array.isArray(arr)) arr = [];
  arr = arr.filter(function (s) { return s !== slug; });
  arr.unshift(slug);
  arr = arr.slice(0, MAX_RECENT);
  setCookie(COOKIE_RECENT, JSON.stringify(arr));
}

/**
 * Get recent product slugs (for rendering on products.php)
 * @returns {string[]} Array of slugs, most recent first
 */
function getRecentProductSlugs() {
  var raw = getCookie(COOKIE_RECENT);
  try {
    if (raw) {
      var arr = JSON.parse(raw);
      return Array.isArray(arr) ? arr : [];
    }
  } catch (e) {}
  return [];
}

/**
 * Update visit_counts: increment count for slug
 * @param {string} slug - Current product slug
 */
function updateVisitCounts(slug) {
  if (!slug) return;
  var raw = getCookie(COOKIE_VISITS);
  var obj = {};
  try {
    if (raw) obj = JSON.parse(raw);
  } catch (e) {
    obj = {};
  }
  if (typeof obj[slug] !== 'number') obj[slug] = 0;
  obj[slug]++;
  setCookie(COOKIE_VISITS, JSON.stringify(obj));
}

/**
 * Get visit counts object
 * @returns {Object} { slug: count, ... }
 */
function getVisitCounts() {
  var raw = getCookie(COOKIE_VISITS);
  try {
    if (raw) return JSON.parse(raw);
  } catch (e) {}
  return {};
}

/**
 * Render "Recently Viewed" thumbnails + links into container
 * Uses window.PRODUCTS_META if on product page, or a global map; on products.php we pass slugs and need meta from PHP
 * On products.php, PRODUCTS_META is not set - so we need to get meta from somewhere. We'll embed PRODUCTS_META in products.php for the front-end.
 * @param {HTMLElement} container - Element to fill
 * @param {string[]} slugs - Array of slugs (from getRecentProductSlugs())
 * @param {Array} meta - Optional: [{ slug, title, image, alt }, ...]. If not provided, uses window.PRODUCTS_META
 */
function renderRecentProducts(container, slugs, meta) {
  meta = meta || (typeof window.PRODUCTS_META !== 'undefined' ? window.PRODUCTS_META : []);
  var basePath = (typeof window.ASSETS_IMAGES !== 'undefined' ? window.ASSETS_IMAGES : '/assets/images') + '/';
  container.innerHTML = '';
  if (!slugs || slugs.length === 0) return;
  var metaBySlug = {};
  if (Array.isArray(meta)) {
    meta.forEach(function (p) {
      metaBySlug[p.slug] = p;
    });
  }
  slugs.forEach(function (slug) {
    var m = metaBySlug[slug];
    var title = m ? m.title : slug;
    var img = m ? m.image : slug + '-1.jpg';
    var alt = m ? m.alt : title;
    var a = document.createElement('a');
    a.href = '/product.php?slug=' + encodeURIComponent(slug);
    a.setAttribute('title', title);
    var imgEl = document.createElement('img');
    imgEl.src = basePath + img;
    imgEl.alt = alt;
    imgEl.loading = 'lazy';
    imgEl.onerror = function() { this.onerror = null; this.src = PLACEHOLDER_IMG; };
    a.appendChild(imgEl);
    var span = document.createElement('span');
    span.textContent = title;
    a.appendChild(span);
    container.appendChild(a);
  });
}

/**
 * Get top 5 most visited slugs by count
 * @returns {Array<{slug: string, count: number}>}
 */
function getTopVisitedSlugs() {
  var counts = getVisitCounts();
  var arr = [];
  for (var slug in counts) {
    if (counts.hasOwnProperty(slug) && typeof counts[slug] === 'number') {
      arr.push({ slug: slug, count: counts[slug] });
    }
  }
  arr.sort(function (a, b) { return b.count - a.count; });
  return arr.slice(0, 5);
}

/**
 * Render "Top 5 Most Visited" into container
 * @param {HTMLElement} container - Element to fill
 * @returns {boolean} True if any items rendered
 */
function renderTopVisited(container) {
  var top = getTopVisitedSlugs();
  if (top.length === 0) {
    container.innerHTML = '';
    return false;
  }
  var meta = typeof window.PRODUCTS_META !== 'undefined' ? window.PRODUCTS_META : [];
  var metaBySlug = {};
  meta.forEach(function (p) {
    metaBySlug[p.slug] = p;
  });
  var basePath = (typeof window.ASSETS_IMAGES !== 'undefined' ? window.ASSETS_IMAGES : '/assets/images') + '/';
  container.innerHTML = '';
  top.forEach(function (item) {
    var m = metaBySlug[item.slug];
    var title = m ? m.title : item.slug;
    var img = m ? m.image : item.slug + '-1.jpg';
    var alt = m ? m.alt : title;
    var a = document.createElement('a');
    a.href = '/product.php?slug=' + encodeURIComponent(item.slug);
    a.setAttribute('title', title + ' (' + item.count + ' visits)');
    var imgEl = document.createElement('img');
    imgEl.src = basePath + img;
    imgEl.alt = alt;
    imgEl.loading = 'lazy';
    imgEl.onerror = function() { this.onerror = null; this.src = PLACEHOLDER_IMG; };
    a.appendChild(imgEl);
    var span = document.createElement('span');
    span.textContent = title + ' (' + item.count + ')';
    a.appendChild(span);
    container.appendChild(a);
  });
  return true;
}
