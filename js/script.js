// Mouse Drag Scroll
const productLists = document.querySelectorAll(".product-list");

productLists.forEach((productList) => {
  let left = 0;
  let X = 0;
  let dragging = false;

  productList.addEventListener("wheel", (e) => {
    e.preventDefault();
    if (e.deltaY > 0) productList.scrollLeft += 100;
    else productList.scrollLeft -= 100;
  });

  productList.addEventListener("mousedown", (e) => {
    e.preventDefault();

    left = productList.scrollLeft;
    X = e.clientX;

    document.addEventListener("mousemove", mousemove);
    document.addEventListener("mouseup", mouseup);
  });

  productList.childNodes.forEach((product) => {
    product.addEventListener("click", (e) => {
      if (dragging) {
        e.preventDefault();
        dragging = false;
      }
    });
  });

  const mousemove = (e) => {
    const dx = e.clientX - X;
    if (dx !== 0) dragging = true;
    productList.scrollLeft = left - dx;
  };

  const mouseup = (e) => {
    document.removeEventListener("mousemove", mousemove);
    document.removeEventListener("mouseup", mouseup);
  };
});
