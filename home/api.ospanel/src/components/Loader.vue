<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
export default {
  name: 'Loader',
};
</script>

<template>
  <div id="loader" class="loader">
    <div>
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
</template>

<style lang="scss">
.loader {
  position: fixed;
  z-index: 9999999;
  top: 0;
  left: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);

  > div {
    position: relative;
    display: inline-block;
    width: 80px;
    height: 80px;

    span {
      position: absolute;
      top: 33px;
      width: 13px;
      height: 13px;
      animation-timing-function: cubic-bezier(0, 1, 1, 0);
      border-radius: 50%;
      background: #fff;
      &:nth-child(1) {
        left: 8px;
        animation: loading1 0.6s infinite;
      }
      &:nth-child(2) {
        left: 8px;
        animation: loading2 0.6s infinite;
      }
      &:nth-child(3) {
        left: 32px;
        animation: loading2 0.6s infinite;
      }
      &:nth-child(4) {
        left: 56px;
        animation: loading3 0.6s infinite;
      }
    }
  }
}

@keyframes loading1 {
  0% { transform: scale(0); }
  100% { transform: scale(1); }
}

@keyframes loading2 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(24px, 0); }
}

@keyframes loading3 {
  0% { transform: scale(1); }
  100% { transform: scale(0); }
}
</style>
