/**
 * Form editor view model module: shows the HubSpot premium strip at the top of the stage.
 * The markup is rendered server-side as the "NsHubSpot-PremiumStrip" form editor template.
 */
export function bootstrap() {
  const template = document.querySelector('template[data-template-name="NsHubSpot-PremiumStrip"]');
  const strip = template?.content.querySelector('.ns-hubspot-premium-strip');
  if (!strip) {
    return;
  }

  const heading = document.querySelector('[data-identifier="stagePanel"] [data-identifier="panelHeading"]');
  if (heading) {
    heading.before(strip.cloneNode(true));
    return;
  }
  document.querySelector('[data-identifier="stageContainerInner"]')?.prepend(strip.cloneNode(true));
}
