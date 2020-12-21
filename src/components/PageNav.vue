<template>
  <div class="nav-items col-12 d-md-flex align-items-end px-1 px-md-2 py-2 mb-2 bg-white shadow-sm">

    <div class="col p-0 d-flex flex-wrap mr-md-2">
      <slot name="menu"></slot>
    </div>

    <div class="p-0 d-flex flex-wrap justify-content-end align-items-center">
      <slot name="btn"></slot>
    </div>

  </div>
</template>