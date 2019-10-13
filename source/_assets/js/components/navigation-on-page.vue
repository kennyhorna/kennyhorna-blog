<template class="sticky top-0 mt-32">
  <div class="w-auto hidden lg:flex pl-6">
    <nav role="aside" class="flex flex-col pl-2" v-if="headings.length">
      <p class="mb-6 text-sm uppercase font-light tracking-wide text-blue">En este artículo</p>

      <a v-for="heading in headings"
         class="mb-4 text-purple-700 hover:text-purple text-sm font-normal leading-normal"
         :href="'#' + heading.id"
         :key="heading.id"
      >{{ heading.textContent }}</a>
    </nav>
  </div>
</template>

<script>
  export default {
    props: {
      headings: Array,
    },
  }
</script>
