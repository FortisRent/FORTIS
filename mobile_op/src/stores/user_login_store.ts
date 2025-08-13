import { defineStore } from 'pinia';

export const user_login_store = defineStore('user_login', {
  state: () => ({
    user_email: "init@state.com",
    is_admin: false,
  }),
  actions: {
    set_email(new_email: string) {
      this.user_email = new_email;
    },
    set_is_admin(is_admin: boolean) {
      this.is_admin = is_admin;
    }
  },
});
