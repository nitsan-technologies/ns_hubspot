/**
 * Form editor view model module: shows the HubSpot premium strip at the top of the stage while
 * "Enable HubSpot" (renderingOptions.hubspotEnable) is on for the form.
 * The markup is rendered server-side as the "NsHubSpot-PremiumStrip" form editor template.
 */
const ENABLE_PROPERTY_PATH = 'renderingOptions.hubspotEnable';
const ENABLED_STRING_VALUES = ['true', '1', 'yes', 'on'];
const REFRESH_EVENTS = [
  'core/formElement/somePropertyChanged',
  'view/undoButton/clicked',
  'view/redoButton/clicked',
  'view/stage/abstract/render/postProcess',
  'view/stage/preview/render/postProcess',
];

export function bootstrap(formEditorApp) {
  const template = document.querySelector('template[data-template-name="NsHubSpot-PremiumStrip"]');
  const templateStrip = template?.content.querySelector('.ns-hubspot-premium-strip');
  if (!templateStrip) {
    return;
  }

  const strip = templateStrip.cloneNode(true);
  const refresh = () => {
    if (!isHubSpotEnabled(formEditorApp.getRootFormElement().get(ENABLE_PROPERTY_PATH))) {
      strip.remove();
    } else if (!strip.isConnected) {
      placeStrip(strip);
    }
  };

  refresh();
  const publisherSubscriber = formEditorApp.getPublisherSubscriber();
  for (const eventName of REFRESH_EVENTS) {
    publisherSubscriber.subscribe(eventName, () => window.setTimeout(refresh));
  }
}

function placeStrip(strip) {
  const heading = document.querySelector('[data-identifier="stagePanel"] [data-identifier="panelHeading"]');
  if (heading) {
    heading.before(strip);
    return;
  }
  document.querySelector('[data-identifier="stageContainerInner"]')?.prepend(strip);
}

/**
 * Same truthy values as ApiFinisher::isHubSpotEnabled().
 */
function isHubSpotEnabled(value) {
  if (typeof value === 'boolean') {
    return value;
  }
  if (typeof value === 'number') {
    return value === 1;
  }
  if (typeof value === 'string') {
    return ENABLED_STRING_VALUES.includes(value.toLowerCase());
  }
  return false;
}
