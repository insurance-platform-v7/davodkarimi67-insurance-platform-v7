export default defineNuxtPlugin(() => {
  const router = useRouter();

  router.onError((error, to, from) => {
    console.error("[Navigation Error]", {
      error,
      to: to.fullPath,
      from: from.fullPath,
    });
  });
});