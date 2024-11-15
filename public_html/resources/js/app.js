import Alpine from "alpinejs";
import { focus } from "@alpinejs/focus";
import { persist } from "@alpinejs/persist";
// import {collapse} from '@alpinejs/collapse';
import ajax from "@imacrayon/alpine-ajax";
import axios from "axios";

import "./bootstrap";
import { createWeb3Modal, defaultWagmiConfig } from "@web3modal/wagmi";

import { mainnet, arbitrum,bsc } from "viem/chains";
import { reconnect, disconnect, watchAccount, getAccount } from "@wagmi/core";
var accountVar = null;
var state = null;
// 1. Define constants
const projectId = "a5a81547d3927389c9f4470fb9968e70";

// 2. Create wagmiConfig
const metadata = {
  name: "Web3Modal",
  description: "Web3Modal Example",
  url: "https://web3modal.com", // origin must match your domain & subdomain
  icons: ["https://avatars.githubusercontent.com/u/37784886"],
};

const chains = [mainnet, arbitrum,bsc];
const config = defaultWagmiConfig({
  chains,
  projectId,
  metadata,
});

reconnect(config);
// 3. Create modal
const modal = createWeb3Modal({
  wagmiConfig: config,
  projectId,
  enableAnalytics: true, // Optional - defaults to your Cloud configuration
  enableOnramp: true, // Optional - false as default
});

window.Alpine = Alpine;

Alpine.plugin([persist, ajax, focus]);

// Generator V2
Alpine.data("generatorV2", () => ({
  itemsSearchStr: "",
  setItemsSearchStr(str) {
    this.itemsSearchStr = str.trim().toLowerCase();
    if (this.itemsSearchStr !== "") {
      this.$el
        .closest(".lqd-generator-sidebar")
        .classList.add("lqd-showing-search-results");
    } else {
      this.$el
        .closest(".lqd-generator-sidebar")
        .classList.remove("lqd-showing-search-results");
    }
  },
  sideNavCollapsed: false,
  /**
   *
   * @param {'collapse' | 'expand'} state
   */
  toggleSideNavCollapse(state) {
    this.sideNavCollapsed = state
      ? state === "collapse"
        ? true
        : false
      : !this.sideNavCollapsed;

    if (this.sideNavCollapsed) {
      tinymce?.activeEditor?.focus();
    }
  },
  generatorStep: 0,
  setGeneratorStep(step) {
    if (step === this.generatorStep) return;
    if (!document.startViewTransition) {
      return (this.generatorStep = Number(step));
    }
    document.startViewTransition(() => (this.generatorStep = Number(step)));
  },
  selectedGenerator: null,
}));

// Chat
Alpine.store("mobileChat", {
  sidebarOpen: false,
  toggleSidebar(state) {
    this.sidebarOpen = state
      ? state === "collapse"
        ? false
        : false
      : !this.sidebarOpen;
  },
});

window.connectWallet = async function (stateParam) {
  state = stateParam;
  if (getAccount(config).isConnected) {
    disconnect(config);
  }
    modal.open();
};

watchAccount(config, {
  onChange(account) {
	if(account?.address===undefined){
		return;
	}
	if(accountVar===null && state===null){
		disconnect(config);
	}
    else if (state === "login") {
      LoginWC(account.address);
    } else if (state === "register") {
      registerWC(account.address);
    }
  }
});

async function LoginWC(account) {
  if (accountVar !== null) {
    return;
  }
  console.log("loginWC");
  accountVar = account;
  try {
    const response = await axios.post("/loginWC", {
      email: account,
      password: account,
    });
    console.log(response);
	if(response.status===200){
		window.location.href = "/dashboard";
	}
  } catch (error) {
    console.error(error);
  }
}

async function registerWC(account) {
  console.log("registerWC");
  accountVar = account;
  try {
    const response = await axios.post("/registerWC/" + account, {
      email: account,
      password: account,
    });
    console.log(response);
	if(response.status===200){
		// alert("Account created successfully!")
    toastr.success(magicai_localize.login_redirect);
		await new Promise((resolve) => {
			setTimeout(() => {
				window.location.href = "/login";
				resolve();
			}, 100);
		});
	}
	else{
		// alert("Account already exists! Please login to continue.")
    toastr.success(magicai_localize.login_redirect);
		await new Promise((resolve) => {
			setTimeout(() => {
				window.location.href = "/login";
				resolve();
			}, 100);
		});
	}
  } catch (error) {
    console.error(error);
  }
}

Alpine.start();
